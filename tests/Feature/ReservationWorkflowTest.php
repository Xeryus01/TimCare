<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_follow_up_zoom_request_and_add_zoom_link(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $reservation = Reservation::create([
            'code' => 'RES-TEST-001',
            'requester_id' => $user->id,
            'room_name' => 'Rapat Koordinasi',
            'purpose' => 'Permintaan ruang Zoom untuk rapat mingguan',
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHour(),
            'status' => 'PENDING',
        ]);

        $this->actingAs($admin)
            ->patch(route('reservations.update', $reservation), [
                'status' => 'APPROVED',
                'zoom_link' => 'https://zoom.us/j/123456789',
                'notes' => 'Silakan gunakan link Zoom ini 10 menit sebelum rapat dimulai.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'APPROVED',
            'zoom_link' => 'https://zoom.us/j/123456789',
        ]);
    }

    /** @test */
    public function user_can_export_ticket_and_zoom_request_data(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $ticketExport = $this->get(route('exports.tickets'));
        $ticketExport->assertOk();
        $ticketExport->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $reservationExport = $this->get(route('exports.reservations'));
        $reservationExport->assertOk();
        $reservationExport->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    /** @test */
    public function user_can_create_reservation_with_nota_dinas_pdf(): void
    {
        \Illuminate\Support\Facades\Storage::fake('local');

        $user = User::factory()->create();

        $pdfFile = \Illuminate\Http\UploadedFile::fake()->create('nota_dinas.pdf', 1000, 'application/pdf');

        $this->actingAs($user)
            ->post(route('reservations.store'), [
                'room_name' => 'Rapat Koordinasi',
                'purpose' => 'Permintaan ruang Zoom untuk rapat mingguan',
                'start_time_local' => now()->addDay()->format('Y-m-d\TH:i'),
                'end_time_local' => now()->addDay()->addHour()->format('Y-m-d\TH:i'),
                'nota_dinas' => $pdfFile,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('reservations', [
            'requester_id' => $user->id,
            'room_name' => 'Rapat Koordinasi',
            'purpose' => 'Permintaan ruang Zoom untuk rapat mingguan',
        ]);

        $reservation = Reservation::where('requester_id', $user->id)->latest()->first();
        $this->assertNotNull($reservation->nota_dinas_path);
        \Illuminate\Support\Facades\Storage::disk('local')->assertExists($reservation->nota_dinas_path);
    }

    /** @test */
    public function unrelated_user_cannot_view_nota_dinas_pdf(): void
    {
        \Illuminate\Support\Facades\Storage::fake('local');

        $requester = User::factory()->create();
        $otherUser = User::factory()->create();

        \Illuminate\Support\Facades\Storage::disk('local')->put('nota_dinas/test.pdf', 'fake-pdf-content');

        $reservation = Reservation::create([
            'code' => 'RES-TEST-002',
            'requester_id' => $requester->id,
            'room_name' => 'Rapat Koordinasi',
            'purpose' => 'Test nota dinas',
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHour(),
            'status' => 'PENDING',
            'nota_dinas_path' => 'nota_dinas/test.pdf',
        ]);

        $this->actingAs($otherUser)
            ->get(route('reservations.nota-dinas', $reservation))
            ->assertForbidden();

        $this->actingAs($requester)
            ->get(route('reservations.nota-dinas', $reservation))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    /** @test */
    public function regular_user_cannot_edit_someone_elses_reservation()
    {
        $requester = User::factory()->create();
        $otherUser = User::factory()->create();

        $reservation = Reservation::create([
            'code' => 'RES-TEST-003',
            'requester_id' => $requester->id,
            'room_name' => 'Rapat Koordinasi',
            'purpose' => 'Test authorization',
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHour(),
            'status' => 'PENDING',
        ]);

        $this->actingAs($otherUser)
            ->get(route('reservations.edit', $reservation))
            ->assertForbidden();
    }

    /** @test */
    public function regular_user_cannot_update_someone_elses_reservation()
    {
        $requester = User::factory()->create();
        $otherUser = User::factory()->create();

        $reservation = Reservation::create([
            'code' => 'RES-TEST-004',
            'requester_id' => $requester->id,
            'room_name' => 'Rapat Koordinasi',
            'purpose' => 'Test authorization',
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHour(),
            'status' => 'PENDING',
        ]);

        $this->actingAs($otherUser)
            ->patch(route('reservations.update', $reservation), [
                'room_name' => 'Updated room name',
                'purpose' => 'Updated purpose',
                'start_time' => now()->addDay()->format('Y-m-d H:i:s'),
                'end_time' => now()->addDay()->addHour()->format('Y-m-d H:i:s'),
            ])
            ->assertForbidden();
    }

    /** @test */
    public function regular_user_cannot_delete_someone_elses_reservation()
    {
        $requester = User::factory()->create();
        $otherUser = User::factory()->create();

        $reservation = Reservation::create([
            'code' => 'RES-TEST-005',
            'requester_id' => $requester->id,
            'room_name' => 'Rapat Koordinasi',
            'purpose' => 'Test authorization',
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHour(),
            'status' => 'PENDING',
        ]);

        $this->actingAs($otherUser)
            ->delete(route('reservations.destroy', $reservation))
            ->assertForbidden();
    }

    /** @test */
    public function regular_user_cannot_view_someone_elses_reservation()
    {
        $requester = User::factory()->create();
        $otherUser = User::factory()->create();

        $reservation = Reservation::create([
            'code' => 'RES-TEST-006',
            'requester_id' => $requester->id,
            'room_name' => 'Rapat Koordinasi',
            'purpose' => 'Test view authorization',
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHour(),
            'status' => 'PENDING',
        ]);

        $this->actingAs($otherUser)
            ->get(route('reservations.show', $reservation))
            ->assertForbidden();
    }
}
