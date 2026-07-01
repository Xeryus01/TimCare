<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Reservation;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        
        $requesters = User::whereIn('id', Reservation::query()->distinct()->pluck('requester_id')->filter())
            ->orderBy('name')->get(['id', 'name']);
        $approvers = User::whereIn('id', Reservation::query()->whereNotNull('approver_id')->distinct()->pluck('approver_id')->filter())
            ->orderBy('name')->get(['id', 'name']);

        $reservations = $q->paginate($perPage)->appends(request()->query());
        return view('reservations.index', compact('reservations', 'sort', 'direction', 'requesters', 'approvers'));
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

        // Handle nota dinas upload
        if ($request->hasFile('nota_dinas')) {
            $file = $request->file('nota_dinas');
            $path = $file->store('nota_dinas', 'public');
            $data['nota_dinas_path'] = $path;
        }

        $reservation = Reservation::create($data);

        // Send notification
        $this->notificationService->notifyReservationCreated($request->user(), $reservation);
        $this->notificationService->notifyAdminsReservationCreated($reservation);

        return redirect()->route('reservations.index')->with('success', 'Pengajuan Zoom berhasil dibuat.');
    }

    public function show(Request $request, Reservation $reservation)
    {
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
                $data['status'] = Reservation::STATUS_APPROVED;
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

        // Handle nota dinas upload if provided
        if ($request->hasFile('nota_dinas')) {
            if ($reservation->nota_dinas_path && Storage::disk('public')->exists($reservation->nota_dinas_path)) {
                Storage::disk('public')->delete($reservation->nota_dinas_path);
            }

            $file = $request->file('nota_dinas');
            $path = $file->store('nota_dinas', 'public');
            $data['nota_dinas_path'] = $path;
        }

        // Prevent setting status to empty/null which can violate NOT NULL DB constraint
        if (array_key_exists('status', $data) && ($data['status'] === '' || $data['status'] === null)) {
            unset($data['status']);
        }

        $reservation->update($data);

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

        $reservation->delete();
        return redirect()->route('reservations.index')->with('success', 'Pengajuan Zoom berhasil dihapus');
    }

    public function showNotaDinas(Request $request, Reservation $reservation)
    {
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
        return response()->file($disk->path($reservation->nota_dinas_path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="nota_dinas_' . $reservation->code . '.pdf"',
        ]);
    }
}
