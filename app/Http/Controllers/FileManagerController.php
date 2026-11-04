<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Reservation;
use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use ZipArchive;

class FileManagerController extends Controller
{
    private string $ticketBackupDir = 'backups/tickets';
    private string $reservationBackupDir = 'backups/reservations';

    /**
     * Halaman File Manager: daftar tiket masalah & tiket zoom beserta berkasnya,
     * dengan aksi backup PDF dan hapus berkas.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'ticket_from' => ['nullable', 'date_format:Y-m-d'],
            'ticket_to' => ['nullable', 'date_format:Y-m-d'],
            'reservation_from' => ['nullable', 'date_format:Y-m-d'],
            'reservation_to' => ['nullable', 'date_format:Y-m-d'],
        ]);

        foreach ([
            ['ticket_from', 'ticket_to'],
            ['reservation_from', 'reservation_to'],
        ] as [$from, $to]) {
            if (! empty($validated[$from]) && ! empty($validated[$to]) && $validated[$from] > $validated[$to]) {
                throw ValidationException::withMessages([
                    $to => 'Tanggal akhir harus sama atau setelah tanggal awal.',
                ]);
            }
        }

        $perPage = in_array((int) $request->input('per_page'), [10, 20, 50], true) ? (int) $request->input('per_page') : 10;

        $ticketQuery = Ticket::with(['requester', 'attachments']);
        if (! empty($validated['ticket_from'])) {
            $ticketQuery->whereDate('created_at', '>=', $validated['ticket_from']);
        }
        if (! empty($validated['ticket_to'])) {
            $ticketQuery->whereDate('created_at', '<=', $validated['ticket_to']);
        }
        $tickets = $ticketQuery->latest()
            ->paginate($perPage, ['*'], 'tpage')
            ->appends($request->except('tpage'));

        $reservationQuery = Reservation::with(['requester']);
        if (! empty($validated['reservation_from'])) {
            $reservationQuery->whereDate('created_at', '>=', $validated['reservation_from']);
        }
        if (! empty($validated['reservation_to'])) {
            $reservationQuery->whereDate('created_at', '<=', $validated['reservation_to']);
        }
        $reservations = $reservationQuery->latest()
            ->paginate($perPage, ['*'], 'rpage')
            ->appends($request->except('rpage'));

        $ticketInfo = [];
        foreach ($tickets as $ticket) {
            $ticketInfo[$ticket->id] = $this->ticketFileInfo($ticket);
        }

        $reservationInfo = [];
        foreach ($reservations as $reservation) {
            $reservationInfo[$reservation->id] = $this->reservationFileInfo($reservation);
        }

        return view('admin.file-manager.index', compact(
            'tickets',
            'reservations',
            'ticketInfo',
            'reservationInfo'
        ));
    }

    /**
     * Buat backup PDF untuk tiket masalah yang dipilih.
     */
    public function backupTickets(Request $request)
    {
        $tickets = $this->recordsForBackup($request, Ticket::query(), 'tiket masalah');
        $tickets->load(['requester', 'assignee', 'asset', 'comments.user', 'attachments']);

        if ($tickets->isEmpty()) {
            return back()->with('error', 'Tiket masalah tidak ditemukan.');
        }

        $files = [];
        foreach ($tickets as $ticket) {
            $relPath = $this->ticketBackupPath($ticket);
            $this->generateTicketPdf($ticket, $relPath);
            $files[$this->safeName($ticket->code) . '.pdf'] = Storage::disk('local')->path($relPath);
        }

        return $this->downloadMany($files, 'backup-tiket-masalah');
    }

    /**
     * Buat backup PDF untuk tiket zoom (reservasi) yang dipilih.
     */
    public function backupReservations(Request $request)
    {
        $reservations = $this->recordsForBackup($request, Reservation::query(), 'pengajuan Zoom');
        $reservations->load(['requester', 'approver']);

        if ($reservations->isEmpty()) {
            return back()->with('error', 'Tiket zoom tidak ditemukan.');
        }

        $files = [];
        foreach ($reservations as $reservation) {
            $relPath = $this->reservationBackupPath($reservation);
            $this->generateReservationPdf($reservation, $relPath);
            $files[$this->safeName($reservation->code) . '.pdf'] = Storage::disk('local')->path($relPath);
        }

        return $this->downloadMany($files, 'backup-tiket-zoom');
    }

    /**
     * Unduh ulang backup PDF tiket masalah yang sudah pernah dibuat.
     */
    public function downloadTicketBackup(Ticket $ticket)
    {
        $relPath = $this->ticketBackupPath($ticket);
        if (! Storage::disk('local')->exists($relPath)) {
            abort(404);
        }

        return response()->download(
            Storage::disk('local')->path($relPath),
            $this->safeName($ticket->code) . '.pdf'
        );
    }

    /**
     * Unduh ulang backup PDF tiket zoom yang sudah pernah dibuat.
     */
    public function downloadReservationBackup(Reservation $reservation)
    {
        $relPath = $this->reservationBackupPath($reservation);
        if (! Storage::disk('local')->exists($relPath)) {
            abort(404);
        }

        return response()->download(
            Storage::disk('local')->path($relPath),
            $this->safeName($reservation->code) . '.pdf'
        );
    }

    /**
     * Hapus seluruh berkas foto/PDF lampiran dari tiket masalah yang dipilih.
     * Data tiket tetap ada; hanya berkas fisik dan baris lampiran yang dihapus.
     */
    public function deleteTicketFiles(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
        ], [
            'ids.required' => 'Pilih minimal satu tiket masalah.',
        ]);

        $tickets = Ticket::with('attachments')->whereIn('id', $validated['ids'])->get();

        $deletedFiles = 0;
        foreach ($tickets as $ticket) {
            foreach ($ticket->attachments as $attachment) {
                $this->deleteFromAllDisks($attachment->file_path);
                $attachment->delete();
                $deletedFiles++;
            }
        }

        return back()->with('success', 'Berhasil menghapus ' . $deletedFiles . ' berkas dari ' . $tickets->count() . ' tiket masalah.');
    }

    /**
     * Hapus berkas nota dinas (PDF) dari tiket zoom yang dipilih.
     */
    public function deleteReservationFiles(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
        ], [
            'ids.required' => 'Pilih minimal satu tiket zoom.',
        ]);

        $reservations = Reservation::whereIn('id', $validated['ids'])->get();

        $deletedFiles = 0;
        foreach ($reservations as $reservation) {
            if ($reservation->nota_dinas_path) {
                $this->deleteFromAllDisks($reservation->nota_dinas_path);
                $reservation->nota_dinas_path = null;
                $reservation->save();
                $deletedFiles++;
            }
        }

        return back()->with('success', 'Berhasil menghapus ' . $deletedFiles . ' berkas nota dinas dari tiket zoom.');
    }

    /* ================= Helpers ================= */

    private function recordsForBackup(Request $request, Builder $query, string $label)
    {
        if ($request->input('scope') === 'period') {
            $validated = $request->validate([
                'scope' => ['required', 'in:period'],
                'date_from' => ['required', 'date_format:Y-m-d'],
                'date_to' => ['required', 'date_format:Y-m-d'],
            ]);

            if ($validated['date_from'] > $validated['date_to']) {
                throw ValidationException::withMessages([
                    'date_to' => 'Tanggal akhir harus sama atau setelah tanggal awal.',
                ]);
            }

            $rangeQuery = $query
                ->whereDate('created_at', '>=', $validated['date_from'])
                ->whereDate('created_at', '<=', $validated['date_to']);

            if ((clone $rangeQuery)->count() > 500) {
                throw ValidationException::withMessages([
                    'date_to' => 'Rentang periode menghasilkan lebih dari 500 data. Persempit rentang tanggal lalu coba lagi.',
                ]);
            }

            $records = $rangeQuery->orderBy('created_at')->get();
        } else {
            $validated = $request->validate([
                'ids' => ['required', 'array', 'min:1', 'max:500'],
                'ids.*' => ['required', 'integer', 'distinct', 'exists:' . $query->getModel()->getTable() . ',id'],
            ], [
                'ids.required' => "Pilih minimal satu {$label} untuk dibackup.",
                'ids.max' => 'Backup manual dibatasi maksimal 500 data sekaligus. Gunakan backup berdasarkan periode.',
            ]);

            $records = $query->whereIn('id', $validated['ids'])->get();
        }

        return $records;
    }

    private function ticketFileInfo(Ticket $ticket): array
    {
        $totalBytes = 0;
        $existing = 0;
        foreach ($ticket->attachments as $attachment) {
            $disk = $this->resolveDisk($attachment->file_path);
            if ($disk !== null) {
                $existing++;
                $totalBytes += (int) $disk->size($attachment->file_path);
            } else {
                $totalBytes += (int) ($attachment->size_bytes ?? 0);
            }
        }

        return [
            'count' => $ticket->attachments->count(),
            'existing' => $existing,
            'totalBytes' => $totalBytes,
            'hasBackup' => Storage::disk('local')->exists($this->ticketBackupPath($ticket)),
        ];
    }

    private function reservationFileInfo(Reservation $reservation): array
    {
        $exists = false;
        $size = 0;
        if ($reservation->nota_dinas_path) {
            $disk = $this->resolveDisk($reservation->nota_dinas_path);
            if ($disk !== null) {
                $exists = true;
                $size = (int) $disk->size($reservation->nota_dinas_path);
            }
        }

        return [
            'hasFile' => (bool) $reservation->nota_dinas_path,
            'exists' => $exists,
            'totalBytes' => $size,
            'hasBackup' => Storage::disk('local')->exists($this->reservationBackupPath($reservation)),
        ];
    }

    private function generateTicketPdf(Ticket $ticket, string $relPath): void
    {
        $images = [];
        foreach ($ticket->attachments as $attachment) {
            $disk = $this->resolveDisk($attachment->file_path);
            if ($disk === null) {
                continue;
            }
            $mime = $attachment->mime_type ?: 'application/octet-stream';
            if (str_starts_with((string) $mime, 'image/')) {
                $images[] = [
                    'name' => $attachment->file_name,
                    'dataUri' => 'data:' . $mime . ';base64,' . base64_encode($disk->get($attachment->file_path)),
                    'mime' => $mime,
                ];
            } else {
                $images[] = [
                    'name' => $attachment->file_name,
                    'dataUri' => null,
                    'mime' => $mime,
                ];
            }
        }

        $pdf = Pdf::loadView('pdf.ticket', [
            'ticket' => $ticket,
            'images' => $images,
        ])->setPaper('a4');

        Storage::disk('local')->makeDirectory($this->ticketBackupDir);
        Storage::disk('local')->put($relPath, $pdf->output());
    }

    private function generateReservationPdf(Reservation $reservation, string $relPath): void
    {
        $pdf = Pdf::loadView('pdf.reservation', [
            'reservation' => $reservation,
        ])->setPaper('a4');

        Storage::disk('local')->makeDirectory($this->reservationBackupDir);
        Storage::disk('local')->put($relPath, $pdf->output());
    }

    private function downloadMany(array $files, string $baseName)
    {
        if (count($files) === 1) {
            $name = array_key_first($files);
            $path = $files[$name];

            return response()->download($path, $name);
        }

        if (class_exists(ZipArchive::class)) {
            $tmpDir = storage_path('app/tmp');
            if (! is_dir($tmpDir)) {
                @mkdir($tmpDir, 0775, true);
            }
            $zipPath = $tmpDir . '/' . $baseName . '-' . date('Ymd-His') . '.zip';

            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \RuntimeException('ZIP backup tidak dapat dibuat.');
            }
            foreach ($files as $name => $path) {
                if (! is_file($path) || ! $zip->addFile($path, $name)) {
                    $zip->close();
                    @unlink($zipPath);
                    throw new \RuntimeException("PDF backup {$name} tidak dapat dimasukkan ke ZIP.");
                }
            }
            if (! $zip->close()) {
                @unlink($zipPath);
                throw new \RuntimeException('ZIP backup tidak dapat diselesaikan.');
            }

            return response()->download($zipPath, basename($zipPath))->deleteFileAfterSend(true);
        }

        return back()->with('success', count($files) . ' PDF backup berhasil dibuat dan tersimpan di server. Ekstensi ZIP tidak tersedia, silakan unduh satu per satu melalui tombol "Unduh PDF" pada tiap baris.');
    }

    private function ticketBackupPath(Ticket $ticket): string
    {
        return $this->ticketBackupDir . '/' . $this->safeName($ticket->code) . '.pdf';
    }

    private function reservationBackupPath(Reservation $reservation): string
    {
        return $this->reservationBackupDir . '/' . $this->safeName($reservation->code) . '.pdf';
    }

    private function safeName(string $value): string
    {
        return preg_replace('/[^A-Za-z0-9_\-]/', '_', $value);
    }

    private function resolveDisk(?string $path)
    {
        if (! $path) {
            return null;
        }
        foreach (['public', 'local'] as $name) {
            if (Storage::disk($name)->exists($path)) {
                return Storage::disk($name);
            }
        }

        return null;
    }

    private function deleteFromAllDisks(?string $path): void
    {
        if (! $path) {
            return;
        }
        foreach (['public', 'local'] as $name) {
            if (Storage::disk($name)->exists($path)) {
                Storage::disk($name)->delete($path);
            }
        }
    }
}
