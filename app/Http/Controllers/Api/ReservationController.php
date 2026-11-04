<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Models\Reservation;
use App\Services\AuditTrailService;
use App\Services\SecureFileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    public function __construct(
        private SecureFileUploadService $fileUploadService,
        private AuditTrailService $auditTrailService
    ) {}
    public function index(Request $request)
    {
        $user = $request->user();
        $q = Reservation::query();

        if (! $user->hasAnyRole(['Admin', 'Teknisi'])) {
            $q->where('requester_id', $user->id);
        }

        return ReservationResource::collection($q->with(['requester', 'approver'])->paginate(15));
    }

    public function show(Request $request, Reservation $reservation)
    {
        $user = $request->user();
        if (! $user->hasAnyRole(['Admin', 'Teknisi']) && $reservation->requester_id !== $user->id) {
            abort(403);
        }
        return new ReservationResource($reservation->load(['requester', 'approver']));
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
                $fileInfo = $this->fileUploadService->storeNotaDinas($request->file('nota_dinas'));
                $data['nota_dinas_path'] = $fileInfo['path'];
            }

            return Reservation::create($data);
        });

        $this->auditTrailService->record($request, $request->user()->id, 'Reservation', $reservation->id, 'CREATED', $reservation->toArray());

        return (new ReservationResource($reservation->load(['requester', 'approver'])))->response()->setStatusCode(201);
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

        unset($data['nota_dinas']);
        $oldNotaDinasPath = null;
        $newNotaDinasPath = null;

        if ($request->hasFile('nota_dinas')) {
            $fileInfo = $this->fileUploadService->storeNotaDinas($request->file('nota_dinas'));
            $newNotaDinasPath = $fileInfo['path'];
            $oldNotaDinasPath = $reservation->nota_dinas_path;
            $data['nota_dinas_path'] = $newNotaDinasPath;
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

        $this->auditTrailService->record($request, $request->user()->id, 'Reservation', $reservation->id, 'UPDATED', [
            'changes' => $reservation->getChanges(),
        ]);

        return new ReservationResource($reservation->fresh(['requester', 'approver']));
    }

    public function destroy(Request $request, Reservation $reservation)
    {
        if (! $request->user()->hasRole('Admin')) {
            abort(403, 'Hanya Admin yang dapat menghapus pengajuan Zoom.');
        }

<<<<<<< HEAD
        $this->deleteStoredFile($reservation->nota_dinas_path);
=======
>>>>>>> 86283643a095c48bf1433b50ced58effc4036f8f
        $reservation->delete();
        $this->auditTrailService->record($request, $request->user()->id, 'Reservation', $reservation->id, 'DELETED');
        return response()->noContent();
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
