<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Log;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaintenanceController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Menampilkan daftar tiket yang menunggu ketersediaan barang (pemeliharaan).
     * Hanya dapat diakses oleh Admin, Teknisi, dan ULP.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (! $user->hasAnyRole(['Admin', 'Teknisi', 'ULP'])) {
            abort(403, 'Anda tidak memiliki akses ke menu pemeliharaan.');
        }

        $q = Ticket::query()
            ->select('tickets.*')
            ->with(['requester', 'assignee', 'asset'])
            ->where('status', Ticket::STATUS_WAITING_PARTS);

        if ($request->filled('code')) {
            $q->where('code', 'like', '%' . $request->input('code') . '%');
        }

        $sort = $request->input('sort');
        $direction = $request->input('direction', 'asc') === 'desc' ? 'desc' : 'asc';

        $sortableColumns = [
            'code' => 'tickets.code',
            'title' => 'tickets.title',
            'requester' => 'requesters.name',
            'created_at' => 'tickets.created_at',
        ];

        if (array_key_exists($sort, $sortableColumns)) {
            if ($sort === 'requester') {
                $q->leftJoin('users as requesters', 'tickets.requester_id', '=', 'requesters.id');
            }
            $q->orderBy($sortableColumns[$sort], $direction);
        } else {
            $q->latest('tickets.created_at');
        }

        $perPage = $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50]) ? (int)$perPage : 10;

        $tickets = $q->paginate($perPage)->appends(request()->query());

        // Cek apakah tiket sudah diproses pemeliharaan (ada log MAINTENANCE_PROCESSED)
        $processedTicketIds = Log::where('entity_type', 'TICKET')
            ->where('action', 'MAINTENANCE_PROCESSED')
            ->whereIn('entity_id', $tickets->pluck('id')->toArray())
            ->pluck('entity_id')
            ->toArray();

        // Tambahkan flag is_processed untuk setiap tiket
        foreach ($tickets as $ticket) {
            $ticket->is_processed = in_array($ticket->id, $processedTicketIds);
        }

        return view('maintenance.index', compact('tickets', 'sort', 'direction'));
    }

    /**
     * ULP menandai tiket sebagai sedang diproses pemeliharaan.
     * Tidak mengubah status tiket, hanya mencatat log.
     */
    public function process(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        if (! $user->hasRole('ULP')) {
            abort(403, 'Hanya petugas ULP yang dapat memproses pemeliharaan.');
        }

        if ($ticket->status !== Ticket::STATUS_WAITING_PARTS) {
            abort(400, 'Tiket ini tidak dalam status menunggu ketersediaan barang.');
        }

        // Log aksi proses pemeliharaan
        Log::create([
            'actor_id' => $user->id,
            'entity_type' => 'TICKET',
            'entity_id' => $ticket->id,
            'action' => 'MAINTENANCE_PROCESSED',
            'meta' => [
                'message' => 'Barang sedang diproses pemeliharaan oleh ULP.',
                'processed_by' => $user->name,
            ],
            'created_at' => now(),
        ]);

        // Tambahkan komentar sistem bahwa barang sedang diproses
        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => '🔧 Barang sedang diproses pemeliharaan oleh ULP (' . $user->name . ').',
            'is_internal' => false,
        ]);

        return redirect()->route('maintenance.index')
            ->with('success', 'Tiket ' . $ticket->code . ' sedang diproses pemeliharaan.');
    }

    /**
     * ULP menyelesaikan pemeliharaan dan mengubah status tiket.
     */
    public function complete(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        if (! $user->hasRole('ULP')) {
            abort(403, 'Hanya petugas ULP yang dapat menyelesaikan pemeliharaan.');
        }

        if ($ticket->status !== Ticket::STATUS_WAITING_PARTS) {
            abort(400, 'Tiket ini tidak dalam status menunggu ketersediaan barang.');
        }

        $data = $request->validate([
            'resolution_status' => 'required|in:' . Ticket::STATUS_SOLVED . ',' . Ticket::STATUS_SOLVED_WITH_NOTES,
            'notes' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,pdf',
        ]);

        $oldStatus = $ticket->status;
        $resolutionStatus = $data['resolution_status'];
        $notes = $data['notes'] ?? '';

        // Upload attachment jika ada
        $attachmentId = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('attachments', 'public');
            $attachment = Attachment::create([
                'ticket_id' => $ticket->id,
                'uploader_id' => $user->id,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'size_bytes' => $file->getSize(),
            ]);
            $attachmentId = $attachment->id;
        }

        // Update status tiket
        $ticket->status = $resolutionStatus;
        if (in_array($resolutionStatus, [Ticket::STATUS_SOLVED, Ticket::STATUS_SOLVED_WITH_NOTES], true)) {
            $ticket->resolved_at = now();
        }
        $ticket->save();

        // Buat komentar penyelesaian
        $message = '✅ Pemeliharaan selesai. Tiket ditutup oleh ULP (' . $user->name . ').';
        if ($resolutionStatus === Ticket::STATUS_SOLVED_WITH_NOTES) {
            $message = '✅ Pemeliharaan selesai dengan catatan. Tiket ditutup oleh ULP (' . $user->name . ').';
        }
        if (!empty($notes)) {
            $message .= "\n\n📝 Catatan: " . $notes;
        }

        $comment = TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $message,
            'is_internal' => false,
        ]);

        // Update attachment dengan comment_id jika ada
        if ($attachmentId) {
            Attachment::where('id', $attachmentId)->update(['comment_id' => $comment->id]);
        }

        // Log penyelesaian
        Log::create([
            'actor_id' => $user->id,
            'entity_type' => 'TICKET',
            'entity_id' => $ticket->id,
            'action' => 'MAINTENANCE_COMPLETED',
            'meta' => [
                'old_status' => $oldStatus,
                'new_status' => $resolutionStatus,
                'notes' => $notes,
                'completed_by' => $user->name,
            ],
            'created_at' => now(),
        ]);

        // Log perubahan status
        Log::create([
            'actor_id' => $user->id,
            'entity_type' => 'TICKET',
            'entity_id' => $ticket->id,
            'action' => 'STATUS_CHANGED',
            'meta' => ['status' => $resolutionStatus],
            'created_at' => now(),
        ]);

        // Kirim notifikasi
        if ($ticket->requester) {
            $this->notificationService->notifyTicketResolved($ticket->requester, $ticket);
        }
        if ($ticket->assignee && $ticket->assignee->id !== $ticket->requester_id) {
            $this->notificationService->notifyTicketResolved($ticket->assignee, $ticket);
        }
        $this->notificationService->notifyAdminsTicketResolved($ticket);

        return redirect()->route('maintenance.index')
            ->with('success', 'Pemeliharaan tiket ' . $ticket->code . ' telah diselesaikan.');
    }
}