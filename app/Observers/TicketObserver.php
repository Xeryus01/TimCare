<?php

namespace App\Observers;

use App\Models\Log;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class TicketObserver
{
    public function created(Ticket $ticket): void
    {
        $this->recordAssignment($ticket, true);
    }

    public function updated(Ticket $ticket): void
    {
        $this->recordAssignment($ticket, $ticket->wasChanged('assignee_id'));
    }

    private function recordAssignment(Ticket $ticket, bool $assignmentChanged): void
    {
        if (! $assignmentChanged || ! $ticket->assignee_id) {
            return;
        }

        Log::create([
            'actor_id' => Auth::id(),
            'entity_type' => 'TICKET',
            'entity_id' => $ticket->id,
            'action' => 'ASSIGNED',
            'meta' => ['assignee_id' => (int) $ticket->assignee_id],
            'created_at' => $ticket->updated_at ?? now(),
        ]);
    }
}
