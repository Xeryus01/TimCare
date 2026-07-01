<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Reservation;
use App\Models\Log;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $q = Reservation::query();

        if (! $user->hasAnyRole(['Admin', 'Teknisi'])) {
            $q->where('requester_id', $user->id);
        }

        return $q->paginate(15);
    }

    public function show(Request $request, Reservation $reservation)
    {
        $user = $request->user();
        if (! $user->hasAnyRole(['Admin', 'Teknisi']) && $reservation->requester_id !== $user->id) {
            abort(403);
        }
        return $reservation;
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

        // Handle nota dinas upload
        if ($request->hasFile('nota_dinas')) {
            $file = $request->file('nota_dinas');
            $path = $file->store('nota_dinas', 'public');
            $data['nota_dinas_path'] = $path;
        }

        $reservation = Reservation::create($data);

        Log::create([
            'actor_id' => $request->user()->id,
            'entity_type' => 'Reservation',
            'entity_id' => $reservation->id,
            'action' => 'CREATED',
            'meta' => $reservation->toArray(),
        ]);

        return response()->json($reservation, 201);
    }

    public function update(UpdateReservationRequest $request, Reservation $reservation)
    {
        $user = $request->user();

        if (! $user->hasAnyRole(['Admin', 'Teknisi']) && $reservation->requester_id !== $user->id) {
            abort(403);
        }

        // Only users with approve reservations permission can update status
        if (! $user->hasPermissionTo('approve reservations') && $request->filled('status')) {
            abort(403, 'Anda tidak memiliki izin untuk menyetujui pengajuan Zoom.');
        }

        $data = $request->validated();
        $isApprover = $user->hasPermissionTo('approve reservations');

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

        if ($isApprover) {
            $data['approver_id'] = $request->user()->id;
        } else {
            unset($data['status'], $data['zoom_link'], $data['zoom_record_link'], $data['notes'], $data['approver_id']);
        }

        $reservation->update($data);

        Log::create([
            'actor_id' => $request->user()->id,
            'entity_type' => 'Reservation',
            'entity_id' => $reservation->id,
            'action' => 'UPDATED',
            'meta' => $reservation->getChanges(),
        ]);

        return response()->json($reservation->fresh(['requester', 'approver']));
    }

    public function destroy(Request $request, Reservation $reservation)
    {
        if (! $request->user()->hasRole('Admin')) {
            abort(403, 'Hanya Admin yang dapat menghapus pengajuan Zoom.');
        }

        $reservation->delete();
        Log::create([
            'actor_id' => $request->user()->id,
            'entity_type' => 'Reservation',
            'entity_id' => $reservation->id,
            'action' => 'DELETED',
            'meta' => [],
        ]);
        return response()->noContent();
    }
}
