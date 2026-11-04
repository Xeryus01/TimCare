<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Reservation;
use App\Models\Ticket;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BulkAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_bulk_assign_active_tickets_and_notify_requester_and_technician(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $technician = User::factory()->create();
        $technician->assignRole('Teknisi');
        $requester = User::factory()->create();
        $tickets = Ticket::factory()->count(2)->create([
            'requester_id' => $requester->id,
            'status' => Ticket::STATUS_OPEN,
        ]);

        $notification = \Mockery::mock(NotificationService::class);
        $notification->shouldReceive('notifyTicketAssigned')->twice()->andReturn(new Notification());
        $notification->shouldReceive('notifyTicketAssignedToRequester')->twice()->andReturn(new Notification());
        $notification->shouldReceive('notifyTicketUpdated')->times(4)->andReturn(new Notification());
        $this->app->instance(NotificationService::class, $notification);

        $this->actingAs($admin)
            ->from(route('tickets.index'))
            ->post(route('tickets.bulk-assign'), [
                'bulk_action' => 'assign',
                'ticket_ids' => $tickets->pluck('id')->all(),
                'assignee_id' => $technician->id,
            ])
            ->assertRedirect(route('tickets.index'))
            ->assertSessionHas('success');

        foreach ($tickets as $ticket) {
            $this->assertDatabaseHas('tickets', [
                'id' => $ticket->id,
                'assignee_id' => $technician->id,
                'status' => Ticket::STATUS_ASSIGNED_DETECT,
            ]);
        }
        $this->assertSame(2, \App\Models\Log::query()
            ->where('entity_type', 'TICKET')
            ->where('action', 'ASSIGNED')
            ->count());
    }

    public function test_admin_can_bulk_assign_pending_zoom_requests(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $technician = User::factory()->create();
        $technician->assignRole('Teknisi');
        $requester = User::factory()->create();
        $reservation = Reservation::create([
            'code' => 'RES-BULK-001',
            'requester_id' => $requester->id,
            'room_name' => 'Rapat Koordinasi',
            'purpose' => 'Pengajuan Zoom untuk rapat.',
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHour(),
            'status' => Reservation::STATUS_PENDING,
        ]);

        $notification = \Mockery::mock(NotificationService::class);
        $notification->shouldReceive('notifyReservationAssigned')->once()->andReturn(new Notification());
        $notification->shouldReceive('notifyReservationApproved')->once()->andReturn(new Notification());
        $this->app->instance(NotificationService::class, $notification);

        $this->actingAs($admin)
            ->from(route('reservations.index'))
            ->post(route('reservations.bulk-assign'), [
                'bulk_action' => 'assign',
                'reservation_ids' => [$reservation->id],
                'approver_id' => $technician->id,
            ])
            ->assertRedirect(route('reservations.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'approver_id' => $technician->id,
            'status' => Reservation::STATUS_APPROVED,
        ]);
        $this->assertDatabaseHas('logs', [
            'entity_type' => 'Reservation',
            'entity_id' => $reservation->id,
            'action' => 'ASSIGNED',
        ]);
    }

    public function test_admin_can_bulk_change_ticket_status_and_resolve_tickets(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $requester = User::factory()->create();
        $ticket = Ticket::factory()->create([
            'requester_id' => $requester->id,
            'status' => Ticket::STATUS_ASSIGNED_DETECT,
        ]);

        $notification = \Mockery::mock(NotificationService::class);
        $notification->shouldReceive('notifyTicketResolved')->once()->andReturn(new Notification());
        $notification->shouldReceive('notifyAdminsTicketResolved')->once();
        $this->app->instance(NotificationService::class, $notification);

        $this->actingAs($admin)
            ->from(route('tickets.index'))
            ->post(route('tickets.bulk-assign'), [
                'bulk_action' => 'status',
                'ticket_ids' => [$ticket->id],
                'status' => Ticket::STATUS_SOLVED,
            ])
            ->assertRedirect(route('tickets.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => Ticket::STATUS_SOLVED,
        ]);
        $this->assertNotNull($ticket->fresh()->resolved_at);
    }

    public function test_admin_can_bulk_reject_zoom_requests(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $requester = User::factory()->create();
        $reservation = Reservation::create([
            'code' => 'RES-BULK-002',
            'requester_id' => $requester->id,
            'room_name' => 'Rapat Koordinasi',
            'purpose' => 'Pengajuan Zoom untuk rapat.',
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHour(),
            'status' => Reservation::STATUS_PENDING,
        ]);

        $this->actingAs($admin)
            ->from(route('reservations.index'))
            ->post(route('reservations.bulk-assign'), [
                'bulk_action' => 'status',
                'reservation_ids' => [$reservation->id],
                'status' => Reservation::STATUS_REJECTED,
            ])
            ->assertRedirect(route('reservations.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => Reservation::STATUS_REJECTED,
        ]);
    }
}
