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
        $q = Reservation::query()->with(['requester', 'approver']);

        if (! $user->hasAnyRole(['Admin', 'Teknisi'])) {
            $q->where('requester_id', $user->id);
        }

        if ($request->filled('status')) {
            $q->where('status', $request->input('status'));
        }

        $perPage = $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50]) ? (int)$perPage : 10;
        
        $reservations = $q->latest()->paginate($perPage)->appends(request()->query());
        return view('reservations.index', compact('reservations'));
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

        if ($reservation->status === Reservation::STATUS_CANCELLED && ! $user->hasRole('Admin')) {
            abort(403, 'Pengajuan Zoom yang dibatalkan tidak dapat diedit kembali kecuali oleh Admin.');
        }

        return view('reservations.edit', compact('reservation'));
    }

    public function update(UpdateReservationRequest $request, Reservation $reservation)
    {
        $user = $request->user();

        if (! $user->hasAnyRole(['Admin', 'Teknisi']) && $reservation->requester_id !== $user->id) {
            abort(403);
        }

        if ($reservation->status === Reservation::STATUS_CANCELLED && ! $user->hasRole('Admin')) {
            abort(403, 'Pengajuan Zoom yang dibatalkan hanya dapat diedit oleh Admin.');
        }

        $data = $request->validated();
        $oldStatus = $reservation->status;
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

            $data['operator_needed'] = $request->boolean('operator_needed');
            $data['breakroom_needed'] = $request->boolean('breakroom_needed');
            $data['participants_count'] = $request->input('participants_count', $reservation->participants_count ?? 1);
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

        $reservation->update($data);

        if ($oldStatus !== $reservation->status && $reservation->status === Reservation::STATUS_APPROVED) {
            $this->notificationService->notifyReservationApproved($reservation->requester, $reservation);
        }

        return redirect()->route('reservations.show', $reservation)->with('success', 'Pengajuan Zoom berhasil diperbarui.');
    }

    public function destroy(Request $request, Reservation $reservation)
    {
        $user = $request->user();

        if (! $user->hasAnyRole(['Admin', 'Teknisi']) && $reservation->requester_id !== $user->id) {
            abort(403);
        }

        $reservation->delete();
        return redirect()->route('reservations.index')->with('success', 'Reservation deleted');
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

        return $disk->response($reservation->nota_dinas_path, 'nota_dinas_' . $reservation->code . '.pdf', [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="nota_dinas_' . $reservation->code . '.pdf"',
        ]);
    }
}
