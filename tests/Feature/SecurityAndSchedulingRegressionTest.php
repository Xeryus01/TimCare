<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\PiketSchedule;
use App\Models\Reservation;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityAndSchedulingRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_piket_edit_and_delete_target_the_selected_row_by_id(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        foreach (['Teknisi A', 'Teknisi B', 'Teknisi C'] as $name) {
            $technician = User::factory()->create(['name' => $name]);
            $technician->assignRole('Teknisi');
        }

        $firstStart = today()->addDays(2);
        $firstEnd = $firstStart->copy()->addDays(3);
        $secondStart = $firstEnd->copy()->addDays(2);
        $secondEnd = $secondStart->copy()->addDays(6);
        $first = PiketSchedule::create([
            'week_start_date' => $firstStart->toDateString(),
            'week_end_date' => $firstEnd->toDateString(),
            'technician_1' => 'Teknisi A',
            'technician_2' => 'Teknisi B',
            'technician_3' => 'Teknisi C',
        ]);
        $second = PiketSchedule::create([
            'week_start_date' => $secondStart->toDateString(),
            'week_end_date' => $secondEnd->toDateString(),
            'technician_1' => 'Teknisi C',
            'technician_2' => 'Teknisi B',
            'technician_3' => 'Teknisi A',
        ]);

        $this->actingAs($admin)
            ->get(route('piket.edit', $first))
            ->assertOk()
            ->assertSee($firstStart->toDateString(), false);

        $this->actingAs($admin)
            ->delete(route('piket.destroy', $first))
            ->assertRedirect(route('piket.index'));

        $this->assertDatabaseMissing('piket_schedules', ['id' => $first->id]);
        $this->assertDatabaseHas('piket_schedules', ['id' => $second->id]);
    }

    public function test_overlapping_piket_range_is_rejected(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        foreach (['Teknisi A', 'Teknisi B', 'Teknisi C'] as $name) {
            $technician = User::factory()->create(['name' => $name]);
            $technician->assignRole('Teknisi');
        }

        $start = today()->addDays(3);
        $end = $start->copy()->addDays(6);
        PiketSchedule::create([
            'week_start_date' => $start->toDateString(),
            'week_end_date' => $end->toDateString(),
            'technician_1' => 'Teknisi A',
            'technician_2' => 'Teknisi B',
            'technician_3' => 'Teknisi C',
        ]);

        $this->actingAs($admin)
            ->from(route('piket.create'))
            ->post(route('piket.store'), [
                'week_start_date' => $end->toDateString(),
                'week_end_date' => $end->copy()->addDays(6)->toDateString(),
                'technician_1' => 'Teknisi A',
                'technician_2' => 'Teknisi B',
                'technician_3' => 'Teknisi C',
            ])
            ->assertRedirect(route('piket.create'))
            ->assertSessionHasErrors('week_start_date');

        $this->assertDatabaseCount('piket_schedules', 1);
    }

    public function test_past_piket_schedule_cannot_be_opened_for_edit_or_updated(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $schedule = PiketSchedule::create([
            'week_start_date' => today()->subDays(14)->toDateString(),
            'week_end_date' => today()->subDays(8)->toDateString(),
            'technician_1' => 'Teknisi A',
            'technician_2' => 'Teknisi B',
            'technician_3' => 'Teknisi C',
        ]);

        $this->actingAs($admin)
            ->get(route('piket.edit', $schedule))
            ->assertRedirect(route('piket.index'))
            ->assertSessionHas('error', 'Jadwal yang sudah terlewat tidak dapat diedit.');

        $this->actingAs($admin)
            ->put(route('piket.update', $schedule), [])
            ->assertForbidden();

        $this->assertDatabaseHas('piket_schedules', [
            'id' => $schedule->id,
            'technician_1' => 'Teknisi A',
        ]);
    }

    public function test_active_piket_schedule_can_still_be_edited(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $start = today()->subDays(2);
        $end = today()->addDays(3);
        $schedule = PiketSchedule::create([
            'week_start_date' => $start->toDateString(),
            'week_end_date' => $end->toDateString(),
            'technician_1' => 'Teknisi A',
            'technician_2' => 'Teknisi B',
            'technician_3' => 'Teknisi C',
        ]);

        $this->actingAs($admin)
            ->get(route('piket.edit', $schedule))
            ->assertOk();

        $this->actingAs($admin)
            ->put(route('piket.update', $schedule), [
                'week_start_date' => $start->toDateString(),
                'week_end_date' => $end->toDateString(),
                'technician_1' => 'Teknisi A Baru',
                'technician_2' => 'Teknisi B',
                'technician_3' => 'Teknisi C',
            ])
            ->assertRedirect(route('piket.index'))
            ->assertSessionHas('success', 'Jadwal piket berhasil diperbarui');

        $this->assertDatabaseHas('piket_schedules', [
            'id' => $schedule->id,
            'technician_1' => 'Teknisi A Baru',
        ]);
    }

    public function test_new_piket_schedule_cannot_be_backdated(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $this->actingAs($admin)
            ->get(route('piket.create'))
            ->assertOk()
            ->assertSee('min="' . today()->toDateString() . '"', false);

        $this->actingAs($admin)
            ->from(route('piket.create'))
            ->post(route('piket.store'), [
                'week_start_date' => today()->subDay()->toDateString(),
                'week_end_date' => today()->addDays(5)->toDateString(),
                'technician_1' => 'Teknisi A',
                'technician_2' => 'Teknisi B',
                'technician_3' => 'Teknisi C',
            ])
            ->assertRedirect(route('piket.create'))
            ->assertSessionHasErrors('week_start_date');

        $this->assertDatabaseCount('piket_schedules', 0);
    }

    public function test_regular_user_cannot_delete_asset_from_web_or_api(): void
    {
        $user = User::factory()->create();
        $asset = Asset::create([
            'asset_code' => 'AST-SEC-001',
            'name' => 'Laptop Uji',
            'type' => 'Laptop',
            'status' => 'ACTIVE',
            'condition' => 'GOOD',
        ]);

        $this->actingAs($user)
            ->delete(route('assets.destroy', $asset))
            ->assertForbidden();

        $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/assets/' . $asset->id)
            ->assertForbidden();

        $this->assertDatabaseHas('assets', ['id' => $asset->id]);
    }

    public function test_unrelated_user_cannot_upload_or_view_ticket_attachment(): void
    {
        Storage::fake('local');

        $requester = User::factory()->create();
        $otherUser = User::factory()->create();
        $ticket = Ticket::factory()->create(['requester_id' => $requester->id]);

        $this->actingAs($otherUser, 'sanctum')
            ->postJson('/api/tickets/' . $ticket->id . '/attachments', [
                'file' => UploadedFile::fake()->create('rahasia.txt', 1, 'text/plain'),
            ])
            ->assertForbidden();
    }

    public function test_internal_ticket_comment_is_hidden_from_requester(): void
    {
        $requester = User::factory()->create();
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $ticket = Ticket::factory()->create(['requester_id' => $requester->id]);

        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'message' => 'CATATAN_INTERNAL_RAHASIA',
            'is_internal' => true,
        ]);

        $this->actingAs($requester)
            ->get(route('tickets.show', $ticket))
            ->assertOk()
            ->assertDontSee('CATATAN_INTERNAL_RAHASIA');
    }

    public function test_reservation_cannot_be_edited_into_a_full_slot(): void
    {
        $start = now()->addDays(10)->startOfHour();
        $end = $start->copy()->addHour();

        for ($i = 1; $i <= Reservation::MAX_CONCURRENT; $i++) {
            Reservation::create([
                'code' => sprintf('RES-FULL-%02d', $i),
                'requester_id' => User::factory()->create()->id,
                'room_name' => 'Zoom ' . $i,
                'purpose' => 'Mengisi kapasitas',
                'start_time' => $start,
                'end_time' => $end,
                'status' => Reservation::STATUS_PENDING,
            ]);
        }

        $requester = User::factory()->create();
        $reservation = Reservation::create([
            'code' => 'RES-EDIT-01',
            'requester_id' => $requester->id,
            'room_name' => 'Zoom Edit',
            'purpose' => 'Jadwal awal',
            'start_time' => $start->copy()->addDay(),
            'end_time' => $end->copy()->addDay(),
            'status' => Reservation::STATUS_PENDING,
        ]);

        $this->actingAs($requester)
            ->from(route('reservations.edit', $reservation))
            ->put(route('reservations.update', $reservation), [
                'start_time_local' => $start->format('Y-m-d\TH:i'),
                'end_time_local' => $end->format('Y-m-d\TH:i'),
            ])
            ->assertRedirect(route('reservations.edit', $reservation))
            ->assertSessionHasErrors('start_time_local');

        $this->assertTrue($reservation->fresh()->start_time->equalTo($start->copy()->addDay()));
    }
}