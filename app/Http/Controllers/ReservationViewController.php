<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Reservation;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ReservationViewController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $q = Reservation::query()->select('reservations.*')->with(['requester', 'approver']);

        if (! $user->hasAnyRole(['Admin', 'Teknisi'])) {
            $q->where('requester_id', $user->id);
        }

        if ($request->filled('status')) {
            $q->where('status', $request->input('status'));
        }
        if ($request->filled('requester_id')) {
            $q->where('requester_id', $request->requester_id);
        }
        if ($request->filled('approver_id')) {
            $q->where('approver_id', $request->approver_id);
        }

        $sort = $request->input('sort');
        $direction = $request->input('direction', 'asc') === 'desc' ? 'desc' : 'asc';

        $sortableColumns = [
            'code' => 'reservations.code',
            'room_name' => 'reservations.room_name',
            'start_time' => 'reservations.start_time',
            'status' => 'reservations.status',
            'requester' => 'requesters.name',
            'approver' => 'approvers.name',
        ];

        if (array_key_exists($sort, $sortableColumns)) {
            if ($sort === 'requester') {
                $q->leftJoin('users as requesters', 'reservations.requester_id', '=', 'requesters.id');
            }
            if ($sort === 'approver') {
                $q->leftJoin('users as approvers', 'reservations.approver_id', '=', 'approvers.id');
            }
            $q->orderBy($sortableColumns[$sort], $direction);
        } else {
            $q->latest('reservations.created_at');
        }

        $perPage = $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50]) ? (int)$perPage : 10;
        
<<<<<<< HEAD
        $filterScope = Reservation::query();
        if (! $user->hasAnyRole(['Admin', 'Teknisi'])) {
            $filterScope->where('requester_id', $user->id);
        }

        $requesters = User::whereIn('id', (clone $filterScope)->distinct()->pluck('requester_id')->filter())
            ->orderBy('name')->get(['id', 'name']);
        $approvers = User::whereIn('id', (clone $filterScope)->whereNotNull('approver_id')->distinct()->pluck('approver_id')->filter())
            ->orderBy('name')->get(['id', 'name']);

        $reservations = $q->paginate($perPage)->appends(request()->query());
        $technicians = User::role('Teknisi')->orderBy('name')->get(['id', 'name']);

        return view('reservations.index', compact('reservations', 'sort', 'direction', 'requesters', 'approvers', 'technicians'));
    }

    public function bulkAssign(Request $request)
    {
        if (! $request->user()->hasRole('Admin')) {
            abort(403, 'Hanya Admin yang dapat menugaskan petugas.');
        }

        $validated = $request->validate([
            'bulk_action' => ['required', 'in:assign,status'],
            'reservation_ids' => ['required', 'array', 'min:1', 'max:50'],
            'reservation_ids.*' => ['required', 'integer', 'distinct', 'exists:reservations,id'],
            'approver_id' => ['required_if:bulk_action,assign', 'nullable', 'integer', 'exists:users,id'],
            'status' => ['required_if:bulk_action,status', 'nullable', 'string', Rule::in(Reservation::statuses())],
        ]);
        $approver = null;
        if ($validated['bulk_action'] === 'assign') {
            $approver = User::role('Teknisi')->find($validated['approver_id']);

            if (! $approver) {
                throw ValidationException::withMessages([
                    'approver_id' => 'Petugas yang dipilih harus memiliki peran Teknisi.',
                ]);
            }
        }

        $reservations = DB::transaction(function () use ($validated) {
            $selectedReservations = Reservation::query()
                ->with('requester')
                ->whereIn('id', $validated['reservation_ids'])
                ->lockForUpdate()
                ->get();

            if ($selectedReservations->count() !== count($validated['reservation_ids'])) {
                throw ValidationException::withMessages([
                    'reservation_ids' => 'Sebagian pengajuan Zoom yang dipilih sudah tidak tersedia.',
                ]);
            }

            $closedStatuses = [
                Reservation::STATUS_COMPLETED,
                Reservation::STATUS_REJECTED,
                Reservation::STATUS_CANCELLED,
            ];
            if ($selectedReservations->contains(fn (Reservation $reservation) => in_array($reservation->status, $closedStatuses, true))) {
                throw ValidationException::withMessages([
                    'reservation_ids' => 'Pengajuan Zoom selesai, ditolak, atau dibatalkan tidak dapat diproses massal.',
                ]);
            }

            if ($validated['bulk_action'] === 'status') {
                $targetStatus = $validated['status'];
                if ($targetStatus === Reservation::STATUS_PENDING) {
                    throw ValidationException::withMessages([
                        'status' => 'Pengajuan Zoom tidak dapat dikembalikan ke status Dibuka melalui aksi massal.',
                    ]);
                }
                if ($targetStatus === Reservation::STATUS_APPROVED
                    && $selectedReservations->contains(fn (Reservation $reservation) => empty($reservation->zoom_link) || empty($reservation->approver_id))) {
                    throw ValidationException::withMessages([
                        'Semua pengajuan harus memiliki petugas dan link Zoom sebelum status Disetujui.',
                    ]);
                }
                if ($targetStatus === Reservation::STATUS_WAITING_MONITORING
                    && $selectedReservations->contains(fn (Reservation $reservation) => empty($reservation->zoom_link))) {
                    throw ValidationException::withMessages([
                        'reservation_ids' => 'Semua pengajuan harus memiliki link Zoom sebelum ditandai siap.',
                    ]);
                }
                if ($targetStatus === Reservation::STATUS_COMPLETED
                    && $selectedReservations->contains(fn (Reservation $reservation) => empty($reservation->zoom_record_link))) {
                    throw ValidationException::withMessages([
                        'reservation_ids' => 'Semua pengajuan harus memiliki link rekaman sebelum diselesaikan.',
                    ]);
                }

                $changedReservations = [];
                foreach ($selectedReservations as $reservation) {
                    if ($reservation->status === $targetStatus) {
                        continue;
                    }

                    $oldStatus = $reservation->status;
                    $reservation->status = $targetStatus;
                    $reservation->save();
                    $changedReservations[] = [$reservation, $oldStatus];
                }

                return $changedReservations;
            }

            $changedReservations = [];
            foreach ($selectedReservations as $reservation) {
                if ((int) $reservation->approver_id === (int) $validated['approver_id']) {
                    continue;
                }

                $oldStatus = $reservation->status;
                $reservation->approver_id = $validated['approver_id'];
                if (! $reservation->getOriginal('approver_id')) {
                    $reservation->status = Reservation::STATUS_APPROVED;
                }
                $reservation->save();
                $changedReservations[] = [$reservation, $oldStatus];
            }

            return $changedReservations;
        });

        foreach ($reservations as [$reservation, $oldStatus]) {
            if ($validated['bulk_action'] === 'assign') {
                $this->notificationService->notifyReservationAssigned($approver, $reservation);
                if ($oldStatus !== $reservation->status && $reservation->status === Reservation::STATUS_APPROVED) {
                    $this->notificationService->notifyReservationApproved($reservation->requester, $reservation);
                }
            } elseif ($reservation->status === Reservation::STATUS_APPROVED) {
                $this->notificationService->notifyReservationApproved($reservation->requester, $reservation);
            } elseif ($reservation->status === Reservation::STATUS_COMPLETED) {
                $this->notificationService->notifyReservationCompleted($reservation->requester, $reservation);
                $this->notificationService->notifyAdminsReservationCompleted($reservation);
            }
        }

        $changedCount = count($reservations);
        $unchangedCount = count($validated['reservation_ids']) - $changedCount;
        $actionLabel = $validated['bulk_action'] === 'assign'
            ? "ditugaskan kepada {$approver->name}"
            : "diubah statusnya menjadi " . Reservation::statusLabels()[$validated['status']];
        $message = $changedCount > 0
            ? "{$changedCount} pengajuan Zoom berhasil {$actionLabel}."
            : "Tidak ada perubahan; semua pengajuan sudah {$actionLabel}.";
        if ($changedCount > 0 && $unchangedCount > 0) {
            $message .= " {$unchangedCount} pengajuan sudah memiliki tindakan tersebut.";
        }

        return back()->with('success', $message);
=======
        $requesters = User::whereIn('id', Reservation::query()->distinct()->pluck('requester_id')->filter())
            ->orderBy('name')->get(['id', 'name']);
        $approvers = User::whereIn('id', Reservation::query()->whereNotNull('approver_id')->distinct()->pluck('approver_id')->filter())
            ->orderBy('name')->get(['id', 'name']);

        $reservations = $q->paginate($perPage)->appends(request()->query());
        return view('reservations.index', compact('reservations', 'sort', 'direction', 'requesters', 'approvers'));
>>>>>>> 86283643a095c48bf1433b50ced58effc4036f8f
    }

    public function create()
    {
        return view('reservations.create');
    }

    public function store(StoreReservationRequest $request)
    {
        $data = $request->validated();
        
        // Add the converted datetime fields if they were provided
        if ($request->filled('start_time_local')) {
            $startTime = $request->input('start_time_local');
            if (strlen($startTime) == 16) {
                $startTime .= ':00';
            }
            $data['start_time'] = $startTime;
        }

        if ($request->filled('end_time_local')) {
            $endTime = $request->input('end_time_local');
            if (strlen($endTime) == 16) {
                $endTime .= ':00';
            }
            $data['end_time'] = $endTime;
        }
        
        $data['requester_id'] = $request->user()->id;
        $data['status'] = Reservation::STATUS_PENDING;
        $data['code'] = Reservation::generateCode();
        $data['operator_needed'] = $request->boolean('operator_needed');
        $data['breakroom_needed'] = $request->boolean('breakroom_needed');
        $data['participants_count'] = $request->input('participants_count', 1);

        $reservation = DB::transaction(function () use ($request, $data) {
            Reservation::whereIn('status', Reservation::capacityStatuses())
                ->lockForUpdate()
                ->get();

            if (Reservation::hasCapacityConflict($data['start_time'], $data['end_time'])) {
                throw ValidationException::withMessages([
                    'start_time_local' => 'Slot Zoom untuk waktu tersebut sudah penuh. Silakan pilih waktu lain.',
                ]);
            }

            if ($request->hasFile('nota_dinas')) {
                $file = $request->file('nota_dinas');
                $data['nota_dinas_path'] = $file->store('nota_dinas', 'local');
            }

            return Reservation::create($data);
        });

        // Send notification
        $this->notificationService->notifyReservationCreated($request->user(), $reservation);
        $this->notificationService->notifyAdminsReservationCreated($reservation);

        return redirect()->route('reservations.index')->with('success', 'Pengajuan Zoom berhasil dibuat.');
    }

    public function show(Request $request, Reservation $reservation)
    {
        $user = $request->user();
        if (! $user->hasAnyRole(['Admin', 'Teknisi']) && $reservation->requester_id !== $user->id) {
            abort(403);
        }

        $reservation->load(['requester', 'approver']);

        $technicians = collect();
        if ($request->user() && $request->user()->hasRole('Admin')) {
            $technicians = \App\Models\User::role('Teknisi')->get();
        }

        return view('reservations.show', compact('reservation', 'technicians'));
    }

    public function edit(Request $request, Reservation $reservation)
    {
        $user = $request->user();

        if (! $user->hasAnyRole(['Admin', 'Teknisi']) && $reservation->requester_id !== $user->id) {
            abort(403);
        }

        if (in_array($reservation->status, [Reservation::STATUS_CANCELLED, Reservation::STATUS_COMPLETED], true)
            && ! $user->hasAnyRole(['Admin', 'Teknisi'])) {
            abort(403, 'Pengajuan Zoom yang sudah selesai atau dibatalkan tidak dapat diedit kembali kecuali oleh Admin atau Teknisi.');
        }

        return view('reservations.edit', compact('reservation'));
    }

    public function update(UpdateReservationRequest $request, Reservation $reservation)
    {
        $user = $request->user();

        if (! $user->hasAnyRole(['Admin', 'Teknisi']) && $reservation->requester_id !== $user->id) {
            abort(403);
        }

        if (in_array($reservation->status, [Reservation::STATUS_CANCELLED, Reservation::STATUS_COMPLETED], true)
            && ! $user->hasAnyRole(['Admin', 'Teknisi'])) {
            abort(403, 'Pengajuan Zoom yang sudah selesai atau dibatalkan hanya dapat diedit oleh Admin atau Teknisi.');
        }

        $data = $request->validated();
        $oldStatus = $reservation->status;
        $oldApproverId = $reservation->approver_id;
        $oldZoomLink = $reservation->zoom_link;
        $isApprover = $user->hasPermissionTo('approve reservations');

        // Regular requester with an existing Zoom link may only update nota dinas or cancel
        if (! $user->hasAnyRole(['Admin', 'Teknisi']) && $reservation->zoom_link) {
            $cancelAllowed = $request->filled('status') && $request->input('status') === Reservation::STATUS_CANCELLED;
            $data = $cancelAllowed ? ['status' => Reservation::STATUS_CANCELLED] : [];
        } else {
            // Only Admin can assign petugas, but both Admin and Teknisi can approve reservations
            if ($request->filled('approver_id') && ! $user->hasRole('Admin')) {
                abort(403, 'Hanya Admin yang dapat menugaskan petugas.');
            }

            if ($request->filled('status') && ! $isApprover) {
                if (! ($request->input('status') === Reservation::STATUS_CANCELLED && $reservation->requester_id === $user->id)) {
                    abort(403, 'Anda tidak memiliki izin untuk mengubah status pengajuan Zoom.');
                }
            }

            if (! $isApprover) {
                if (! (isset($data['status']) && $data['status'] === Reservation::STATUS_CANCELLED && $reservation->requester_id === $user->id)) {
                    unset($data['status']);
                }

                unset($data['zoom_link'], $data['zoom_record_link'], $data['notes'], $data['approver_id']);
            }
        }

        if ($user->hasRole('Admin')) {
            if ($request->has('approver_id')) {
                $data['approver_id'] = $request->input('approver_id') ?: null;
            } else {
                unset($data['approver_id']);
            }
        } elseif ($isApprover) {
            $data['approver_id'] = $request->user()->id;
        }

        if (! (! $user->hasAnyRole(['Admin', 'Teknisi']) && $reservation->zoom_link)) {
            if ($request->filled('start_time_local')) {
                $startTime = $request->input('start_time_local');
                if (strlen($startTime) == 16) {
                    $startTime .= ':00';
                }
                $data['start_time'] = $startTime;
            }

            if ($request->filled('end_time_local')) {
                $endTime = $request->input('end_time_local');
                if (strlen($endTime) == 16) {
                    $endTime .= ':00';
                }
                $data['end_time'] = $endTime;
            }

            if ($request->exists('operator_needed')) {
                $data['operator_needed'] = $request->boolean('operator_needed');
            }

            if ($request->exists('breakroom_needed')) {
                $data['breakroom_needed'] = $request->boolean('breakroom_needed');
            }

            if ($request->exists('participants_count')) {
                $data['participants_count'] = $request->input('participants_count', $reservation->participants_count ?? 1);
            }
        }

        if ($isApprover) {
            if ($request->filled('zoom_link') && empty($reservation->zoom_link)) {
                // Saat link Zoom mulai terisi, status berpindah ke "Zoom Siap".
                $data['status'] = Reservation::STATUS_WAITING_MONITORING;
            }

            if ($request->filled('zoom_record_link') && empty($reservation->zoom_record_link)) {
                $data['status'] = Reservation::STATUS_COMPLETED;
            }
        }

        if ($user->hasRole('Admin') && $request->filled('approver_id') && empty($reservation->approver_id)) {
            $data['status'] = Reservation::STATUS_APPROVED;
        }

        if (isset($data['status']) && $data['status'] === Reservation::STATUS_CANCELLED) {
            $data['status'] = Reservation::STATUS_CANCELLED;
        }

        $oldNotaDinasPath = null;
        $newNotaDinasPath = null;

        // Simpan berkas baru lebih dulu. Berkas lama baru dihapus setelah
        // transaksi database berhasil agar kegagalan validasi tidak kehilangan data.
        if ($request->hasFile('nota_dinas')) {
            $file = $request->file('nota_dinas');
            $newNotaDinasPath = $file->store('nota_dinas', 'local');
            $oldNotaDinasPath = $reservation->nota_dinas_path;
            $data['nota_dinas_path'] = $newNotaDinasPath;
        }

        // Prevent setting status to empty/null which can violate NOT NULL DB constraint
        if (array_key_exists('status', $data) && ($data['status'] === '' || $data['status'] === null)) {
            unset($data['status']);
        }

        try {
            DB::transaction(function () use ($reservation, $data) {
                Reservation::whereIn('status', Reservation::capacityStatuses())
                    ->lockForUpdate()
                    ->get();

                $finalStatus = $data['status'] ?? $reservation->status;
                $finalStart = $data['start_time'] ?? $reservation->start_time;
                $finalEnd = $data['end_time'] ?? $reservation->end_time;

                if (in_array($finalStatus, Reservation::capacityStatuses(), true)
                    && Reservation::hasCapacityConflict($finalStart, $finalEnd, $reservation->id)) {
                    throw ValidationException::withMessages([
                        'start_time_local' => 'Slot Zoom untuk waktu tersebut sudah penuh. Silakan pilih waktu lain.',
                    ]);
                }

                $reservation->update($data);
            });
        } catch (\Throwable $exception) {
            if ($newNotaDinasPath) {
                $this->deleteStoredFile($newNotaDinasPath);
            }
            throw $exception;
        }

        if ($oldNotaDinasPath && $oldNotaDinasPath !== $newNotaDinasPath) {
            $this->deleteStoredFile($oldNotaDinasPath);
        }

        if ($oldApproverId !== $reservation->approver_id && $reservation->approver_id) {
            $approver = User::find($reservation->approver_id);
            if ($approver) {
                $this->notificationService->notifyReservationAssigned($approver, $reservation);
            }
        }

        if ($oldZoomLink !== $reservation->zoom_link && ! empty($reservation->zoom_link)) {
            $this->notificationService->notifyReservationZoomLinkAvailable($reservation->requester, $reservation);
        }

        if ($oldStatus !== $reservation->status) {
            if ($reservation->status === Reservation::STATUS_APPROVED) {
                $this->notificationService->notifyReservationApproved($reservation->requester, $reservation);
            }
            if ($reservation->status === Reservation::STATUS_COMPLETED) {
                $this->notificationService->notifyReservationCompleted($reservation->requester, $reservation);
                $this->notificationService->notifyAdminsReservationCompleted($reservation);
            }
        }

        return redirect()->route('reservations.show', $reservation)->with('success', 'Pengajuan Zoom berhasil diperbarui.');
    }

    public function destroy(Request $request, Reservation $reservation)
    {
        if (! $request->user()->hasRole('Admin')) {
            abort(403, 'Hanya Admin yang dapat menghapus pengajuan Zoom.');
        }

        $this->deleteStoredFile($reservation->nota_dinas_path);
        $reservation->delete();
        return redirect()->route('reservations.index')->with('success', 'Pengajuan Zoom berhasil dihapus');
    }

    public function showNotaDinas(Request $request, Reservation $reservation)
    {
        $user = $request->user();
        if (! $user->hasAnyRole(['Admin', 'Teknisi']) && $reservation->requester_id !== $user->id) {
            abort(403);
        }

        if (!$reservation->nota_dinas_path) {
            abort(404);
        }

        $diskName = Storage::disk('public')->exists($reservation->nota_dinas_path) ? 'public' : 'local';
        $disk = Storage::disk($diskName);

        if (! $disk->exists($reservation->nota_dinas_path)) {
            abort(404);
        }

        // Sajikan via response()->file() (BinaryFileResponse) agar mendukung HTTP Range request,
        // sehingga PDF nota dinas dimuat andal di browser (StreamedResponse tidak mendukung range).
<<<<<<< HEAD
        $response = response()->file($disk->path($reservation->nota_dinas_path), [
=======
        return response()->file($disk->path($reservation->nota_dinas_path), [
>>>>>>> 86283643a095c48bf1433b50ced58effc4036f8f
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="nota_dinas_' . $reservation->code . '.pdf"',
        ]);

        // Cache + validasi kondisional (ETag/Last-Modified) agar PDF tidak di-unduh ulang tiap reload.
        $response->setAutoEtag();
        $response->setAutoLastModified();
        $response->setPrivate();
        $response->setMaxAge(31536000);
        $response->headers->addCacheControlDirective('immutable');
        $response->isNotModified($request);

        return $response;
    }

    private function deleteStoredFile(?string $path): void
    {
        if (! $path) {
            return;
        }

        foreach (['local', 'public'] as $diskName) {
            if (Storage::disk($diskName)->exists($path)) {
                Storage::disk($diskName)->delete($path);
            }
        }
    }
}
