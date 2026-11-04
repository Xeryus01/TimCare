<?php

namespace App\Services;

use App\Models\Log;
use App\Models\PiketSchedule;
use App\Models\Reservation;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class PiketWorkloadService
{
    public function forSchedule(?PiketSchedule $schedule, bool $includeWorkload): Collection
    {
        if (! $schedule) {
            return collect();
        }

        $weekStart = Carbon::parse($schedule->week_start_date)->startOfDay();
        $weekEnd = $schedule->week_end_date
            ? Carbon::parse($schedule->week_end_date)->endOfDay()
            : $weekStart->copy()->addDays(6)->endOfDay();
        $names = $schedule->scheduledUserNames();
        $normalizedNames = collect($names)
            ->map(fn (string $name) => mb_strtolower(trim($name)))
            ->filter()
            ->unique()
            ->values();

        $usersByName = $normalizedNames->isEmpty()
            ? collect()
            : User::withTrashed()
                ->where(function ($query) use ($normalizedNames) {
                    foreach ($normalizedNames as $name) {
                        $query->orWhereRaw('LOWER(TRIM(name)) = ?', [$name]);
                    }
                })
                ->orderBy('id')
                ->get(['id', 'name'])
                ->groupBy(fn (User $user) => mb_strtolower(trim($user->name)));

        $technicianIds = $normalizedNames
            ->map(fn (string $name) => $usersByName->get($name)?->count() === 1
                ? $usersByName->get($name)->first()->id
                : null)
            ->filter()
            ->values();

        $workItemsByType = [
            'ticket' => [],
            'reservation' => [],
        ];
        if ($includeWorkload && $technicianIds->isNotEmpty()) {
            $addWorkItem = function (string $type, int $userId, int $workId) use (&$workItemsByType, $technicianIds): void {
                if ($technicianIds->contains($userId)) {
                    $workItemsByType[$type][$userId][$workId] = true;
                }
            };

            $currentAssignees = [
                'ticket' => [],
                'reservation' => [],
            ];

            Ticket::query()
                ->whereIn('assignee_id', $technicianIds)
                ->get(['id', 'assignee_id', 'created_at'])
                ->each(function (Ticket $ticket) use (&$currentAssignees, $addWorkItem, $weekStart, $weekEnd): void {
                    $userId = (int) $ticket->assignee_id;
                    $currentAssignees['ticket'][(int) $ticket->id] = $userId;

                    if ($ticket->created_at && $ticket->created_at->gte($weekStart) && $ticket->created_at->lte($weekEnd)) {
                        $addWorkItem('ticket', $userId, (int) $ticket->id);
                    }
                });

            Reservation::query()
                ->whereIn('approver_id', $technicianIds)
                ->get(['id', 'approver_id', 'created_at'])
                ->each(function (Reservation $reservation) use (&$currentAssignees, $addWorkItem, $weekStart, $weekEnd): void {
                    $userId = (int) $reservation->approver_id;
                    $currentAssignees['reservation'][(int) $reservation->id] = $userId;

                    if ($reservation->created_at && $reservation->created_at->gte($weekStart) && $reservation->created_at->lte($weekEnd)) {
                        $addWorkItem('reservation', $userId, (int) $reservation->id);
                    }
                });

            Log::query()
                ->whereIn('entity_type', ['TICKET', 'Reservation'])
                ->where('action', 'ASSIGNED')
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->get(['entity_type', 'entity_id', 'meta'])
                ->each(function (Log $log) use ($addWorkItem, $currentAssignees): void {
                    $meta = $log->meta ?? [];
                    $type = $log->entity_type === 'TICKET' ? 'ticket' : 'reservation';
                    $userId = (int) ($meta[$type === 'ticket' ? 'assignee_id' : 'approver_id'] ?? 0);
                    $workId = (int) $log->entity_id;

                    if ($userId > 0 && ($currentAssignees[$type][$workId] ?? null) === $userId) {
                        $addWorkItem($type, $userId, $workId);
                    }
                });
        }

        $technicians = collect($names)->map(function (string $name) use (
            $usersByName,
            $workItemsByType,
            $includeWorkload
        ) {
            $normalizedName = mb_strtolower(trim($name));
            $matchingUsers = $usersByName->get($normalizedName, collect());
            $technician = $matchingUsers->count() === 1 ? $matchingUsers->first() : null;
            $technicianId = $technician?->id;

            return [
                'name' => $name,
                'ticket_count' => $includeWorkload && $technicianId
                    ? count($workItemsByType['ticket'][$technicianId] ?? [])
                    : 0,
                'zoom_count' => $includeWorkload && $technicianId
                    ? count($workItemsByType['reservation'][$technicianId] ?? [])
                    : 0,
                'identity_ambiguous' => $matchingUsers->count() > 1,
                'identity_missing' => $matchingUsers->isEmpty(),
            ];
        })->values();

        return collect([[
            'week_start_date' => $weekStart->format('d M Y'),
            'week_end_date' => $weekEnd->format('d M Y'),
            'technicians' => $technicians->all(),
            'is_active' => true,
        ]]);
    }
}
