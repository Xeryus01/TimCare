<?php

namespace Tests\Feature;

use App\Models\Log;
use App\Models\PiketSchedule;
use App\Models\Reservation;
use App\Models\Ticket;
use App\Models\User;
use App\Services\PiketWorkloadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PiketWorkloadServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_workload_counts_unique_assignments_inside_the_schedule_week(): void
    {
        $technician = User::factory()->create(['name' => 'Teknisi Jadwal']);
        $technician->assignRole('Teknisi');
        $schedule = PiketSchedule::create([
            'week_start_date' => '2026-11-02',
            'week_end_date' => '2026-11-08',
            'technician_1' => $technician->name,
        ]);
        $tickets = collect([101, 102, 103])->mapWithKeys(fn (int $id) => [
            $id => Ticket::withoutEvents(fn () => Ticket::factory()->create([
                'assignee_id' => $technician->id,
                'created_at' => '2026-10-30 08:00:00',
                'updated_at' => '2026-10-30 08:00:00',
            ])),
        ]);
        $reservation = Reservation::withoutEvents(fn () => Reservation::create([
            'code' => 'RES-WORKLOAD-201',
            'requester_id' => User::factory()->create()->id,
            'approver_id' => $technician->id,
            'room_name' => 'Ruang Rapat',
            'purpose' => 'Rapat koordinasi',
            'start_time' => '2026-11-05 09:00:00',
            'end_time' => '2026-11-05 10:00:00',
            'status' => Reservation::STATUS_PENDING,
            'created_at' => '2026-10-30 08:00:00',
            'updated_at' => '2026-10-30 08:00:00',
        ]));

        Log::create([
            'entity_type' => 'TICKET',
            'entity_id' => $tickets[101]->id,
            'action' => 'ASSIGNED',
            'meta' => ['assignee_id' => $technician->id],
            'created_at' => '2026-11-02 08:00:00',
        ]);
        Log::create([
            'entity_type' => 'TICKET',
            'entity_id' => $tickets[101]->id,
            'action' => 'ASSIGNED',
            'meta' => ['assignee_id' => $technician->id],
            'created_at' => '2026-11-03 08:00:00',
        ]);
        Log::create([
            'entity_type' => 'TICKET',
            'entity_id' => $tickets[102]->id,
            'action' => 'ASSIGNED',
            'meta' => ['assignee_id' => $technician->id],
            'created_at' => '2026-11-08 23:59:59',
        ]);
        Log::create([
            'entity_type' => 'TICKET',
            'entity_id' => $tickets[103]->id,
            'action' => 'ASSIGNED',
            'meta' => ['assignee_id' => $technician->id],
            'created_at' => '2026-11-09 00:00:00',
        ]);
        Log::create([
            'entity_type' => 'Reservation',
            'entity_id' => $reservation->id,
            'action' => 'ASSIGNED',
            'meta' => ['approver_id' => $technician->id],
            'created_at' => '2026-11-04 10:00:00',
        ]);

        $result = app(PiketWorkloadService::class)->forSchedule($schedule, true)->first();
        $workload = $result['technicians'][0];

        $this->assertSame(2, $workload['ticket_count']);
        $this->assertSame(1, $workload['zoom_count']);
        $this->assertFalse($workload['identity_ambiguous']);
    }

    public function test_ambiguous_roster_names_are_not_attributed_to_the_wrong_account(): void
    {
        $first = User::factory()->create(['name' => 'Nama Sama']);
        $second = User::factory()->create(['name' => 'Nama Sama']);
        $schedule = PiketSchedule::create([
            'week_start_date' => '2026-11-02',
            'week_end_date' => '2026-11-08',
            'technician_1' => 'Nama Sama',
        ]);
        Log::create([
            'entity_type' => 'TICKET',
            'entity_id' => 301,
            'action' => 'ASSIGNED',
            'meta' => ['assignee_id' => $first->id],
            'created_at' => '2026-11-04 10:00:00',
        ]);

        $workload = app(PiketWorkloadService::class)->forSchedule($schedule, true)->first()['technicians'][0];

        $this->assertSame(0, $workload['ticket_count']);
        $this->assertTrue($workload['identity_ambiguous']);
    }

    public function test_missing_roster_account_is_reported_as_unmapped(): void
    {
        $schedule = PiketSchedule::create([
            'week_start_date' => '2026-11-02',
            'week_end_date' => '2026-11-08',
            'technician_1' => 'Akun Tidak Ada',
        ]);

        $workload = app(PiketWorkloadService::class)->forSchedule($schedule, true)->first()['technicians'][0];

        $this->assertSame(0, $workload['ticket_count']);
        $this->assertTrue($workload['identity_missing']);
    }

    public function test_existing_assigned_items_without_assignment_logs_are_counted_for_the_week(): void
    {
        $technician = User::factory()->create(['name' => 'Petugas Eksisting']);
        $schedule = PiketSchedule::create([
            'week_start_date' => '2026-11-02',
            'week_end_date' => '2026-11-08',
            'technician_1' => $technician->name,
        ]);
        Ticket::withoutEvents(fn () => Ticket::factory()->create([
            'assignee_id' => $technician->id,
            'created_at' => '2026-11-04 07:47:00',
            'updated_at' => '2026-11-04 07:47:00',
        ]));
        Ticket::withoutEvents(fn () => Ticket::factory()->create([
            'assignee_id' => $technician->id,
            'created_at' => '2026-06-24 08:03:13',
            'updated_at' => '2026-11-02 00:53:16',
        ]));
        Reservation::withoutEvents(fn () => Reservation::create([
            'code' => 'RES-LEGACY-001',
            'requester_id' => User::factory()->create()->id,
            'approver_id' => $technician->id,
            'room_name' => 'Ruang Rapat',
            'purpose' => 'Rapat koordinasi',
            'start_time' => '2026-11-05 09:00:00',
            'end_time' => '2026-11-05 10:00:00',
            'status' => Reservation::STATUS_PENDING,
            'created_at' => '2026-11-04 08:00:00',
            'updated_at' => '2026-11-04 08:00:00',
        ]));

        $workload = app(PiketWorkloadService::class)->forSchedule($schedule, true)->first()['technicians'][0];

        $this->assertSame(1, $workload['ticket_count']);
        $this->assertSame(1, $workload['zoom_count']);
    }

    public function test_deleted_and_reassigned_tickets_only_count_for_the_current_assignee(): void
    {
        $previousTechnician = User::factory()->create(['name' => 'Petugas Sebelumnya']);
        $currentTechnician = User::factory()->create(['name' => 'Petugas Baru']);
        $schedule = PiketSchedule::create([
            'week_start_date' => '2026-11-02',
            'week_end_date' => '2026-11-08',
            'technician_1' => $previousTechnician->name,
            'technician_2' => $currentTechnician->name,
        ]);
        $deletedTicket = Ticket::withoutEvents(fn () => Ticket::factory()->create([
            'assignee_id' => $previousTechnician->id,
            'created_at' => '2026-10-30 08:00:00',
            'updated_at' => '2026-10-30 08:00:00',
        ]));
        $reassignedTicket = Ticket::withoutEvents(fn () => Ticket::factory()->create([
            'assignee_id' => $previousTechnician->id,
            'created_at' => '2026-10-30 08:00:00',
            'updated_at' => '2026-10-30 08:00:00',
        ]));
        foreach ([$deletedTicket, $reassignedTicket] as $ticket) {
            Log::create([
                'entity_type' => 'TICKET',
                'entity_id' => $ticket->id,
                'action' => 'ASSIGNED',
                'meta' => ['assignee_id' => $previousTechnician->id],
                'created_at' => '2026-11-03 08:00:00',
            ]);
        }
        $deletedTicket->delete();
        Ticket::withoutEvents(function () use ($reassignedTicket, $currentTechnician): void {
            $reassignedTicket->assignee_id = $currentTechnician->id;
            $reassignedTicket->save();
        });
        Log::create([
            'entity_type' => 'TICKET',
            'entity_id' => $reassignedTicket->id,
            'action' => 'ASSIGNED',
            'meta' => ['assignee_id' => $currentTechnician->id],
            'created_at' => '2026-11-04 08:00:00',
        ]);

        $workloads = app(PiketWorkloadService::class)->forSchedule($schedule, true)->first()['technicians'];

        $this->assertSame(0, $workloads[0]['ticket_count']);
        $this->assertSame(1, $workloads[1]['ticket_count']);
    }

    public function test_deleted_and_reassigned_zoom_requests_only_count_for_the_current_assignee(): void
    {
        $previousTechnician = User::factory()->create(['name' => 'Petugas Zoom Sebelumnya']);
        $currentTechnician = User::factory()->create(['name' => 'Petugas Zoom Baru']);
        $schedule = PiketSchedule::create([
            'week_start_date' => '2026-11-02',
            'week_end_date' => '2026-11-08',
            'technician_1' => $previousTechnician->name,
            'technician_2' => $currentTechnician->name,
        ]);
        $requester = User::factory()->create();
        $deletedReservation = $this->createWorkloadReservation($requester, $previousTechnician, 'RES-WORKLOAD-DELETE');
        $reassignedReservation = $this->createWorkloadReservation($requester, $previousTechnician, 'RES-WORKLOAD-REASSIGN');

        foreach ([$deletedReservation, $reassignedReservation] as $reservation) {
            Log::create([
                'entity_type' => 'Reservation',
                'entity_id' => $reservation->id,
                'action' => 'ASSIGNED',
                'meta' => ['approver_id' => $previousTechnician->id],
                'created_at' => '2026-11-03 08:00:00',
            ]);
        }

        $deletedReservation->delete();
        Reservation::withoutEvents(function () use ($reassignedReservation, $currentTechnician): void {
            $reassignedReservation->approver_id = $currentTechnician->id;
            $reassignedReservation->save();
        });
        Log::create([
            'entity_type' => 'Reservation',
            'entity_id' => $reassignedReservation->id,
            'action' => 'ASSIGNED',
            'meta' => ['approver_id' => $currentTechnician->id],
            'created_at' => '2026-11-04 08:00:00',
        ]);

        $workloads = app(PiketWorkloadService::class)->forSchedule($schedule, true)->first()['technicians'];

        $this->assertSame(0, $workloads[0]['zoom_count']);
        $this->assertSame(1, $workloads[1]['zoom_count']);
    }

    private function createWorkloadReservation(User $requester, User $approver, string $code): Reservation
    {
        return Reservation::withoutEvents(fn () => Reservation::create([
            'code' => $code,
            'requester_id' => $requester->id,
            'approver_id' => $approver->id,
            'room_name' => 'Ruang Rapat',
            'purpose' => 'Rapat koordinasi',
            'start_time' => '2026-11-05 09:00:00',
            'end_time' => '2026-11-05 10:00:00',
            'status' => Reservation::STATUS_PENDING,
            'created_at' => '2026-10-30 08:00:00',
            'updated_at' => '2026-10-30 08:00:00',
        ]));
    }
}
