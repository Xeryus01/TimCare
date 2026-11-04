<?php

namespace App\Observers;

use App\Models\Log;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;

class ReservationObserver
{
    public function created(Reservation $reservation): void
    {
        $this->recordAssignment($reservation, true);
    }

    public function updated(Reservation $reservation): void
    {
        $this->recordAssignment($reservation, $reservation->wasChanged('approver_id'));
    }

    private function recordAssignment(Reservation $reservation, bool $assignmentChanged): void
    {
        if (! $assignmentChanged || ! $reservation->approver_id) {
            return;
        }

        Log::create([
            'actor_id' => Auth::id(),
            'entity_type' => 'Reservation',
            'entity_id' => $reservation->id,
            'action' => 'ASSIGNED',
            'meta' => ['approver_id' => (int) $reservation->approver_id],
            'created_at' => $reservation->updated_at ?? now(),
        ]);
    }
}
