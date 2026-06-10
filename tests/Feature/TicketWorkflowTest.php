<?php

namespace Tests\Feature;

use App\Models\Attachment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TicketWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function regular_user_can_create_ticket_and_defaults_to_open()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/tickets', [
            'category' => 'HARDWARE_SUPPORT',
            'title' => 'My printer is broken',
            'description' => 'It will not print',
            'priority' => 'HIGH',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', Ticket::STATUS_OPEN);

        $this->assertDatabaseHas('tickets', [
            'requester_id' => $user->id,
            'status' => Ticket::STATUS_OPEN,
        ]);
    }

    /** @test */
    public function technician_can_update_status_and_resolved_at_is_set()
    {
        $tech = User::factory()->create();
        $tech->assignRole('Teknisi');

        $ticket = Ticket::factory()->create(['status' => Ticket::STATUS_OPEN]);

        $this->actingAs($tech);
        $response = $this->patchJson("/api/tickets/{$ticket->id}", [
            'status' => Ticket::STATUS_SOLVED,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', Ticket::STATUS_SOLVED);

        $this->assertNotNull($ticket->fresh()->resolved_at);
    }

    /** @test */
    public function regular_user_cannot_change_status_via_api()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create(['status' => Ticket::STATUS_OPEN]);
        $this->actingAs($user);

        $this->patchJson("/api/tickets/{$ticket->id}", [
            'status' => Ticket::STATUS_SOLVED,
        ])->assertStatus(403);
    }

    /** @test */
    public function adding_comment_with_status_changes_ticket()
    {
        $tech = User::factory()->create();
        $tech->assignRole('Teknisi');
        $ticket = Ticket::factory()->create(['status' => Ticket::STATUS_ASSIGNED_DETECT]);

        $this->actingAs($tech);
        $this->postJson("/api/tickets/{$ticket->id}/comments", [
            'message' => 'Investigated and fixed',
            'status' => Ticket::STATUS_SOLVED_WITH_NOTES,
        ])->assertStatus(201);

        $this->assertEquals(Ticket::STATUS_SOLVED_WITH_NOTES, $ticket->fresh()->status);
    }

    /** @test */
    public function api_index_can_be_filtered_by_status()
    {
        $user = User::factory()->create();
        $user->assignRole('Teknisi');
        Ticket::factory()->count(3)->create(['status' => Ticket::STATUS_OPEN]);
        Ticket::factory()->count(2)->create(['status' => Ticket::STATUS_SOLVED]);

        $this->actingAs($user);
        $res = $this->getJson('/api/tickets?status=' . Ticket::STATUS_SOLVED);
        $res->assertStatus(200);
        $data = $res->json('data');
        $this->assertCount(2, $data);
    }

    /** @test */
    public function dashboard_summary_reflects_new_status_counts()
    {
        $user = User::factory()->create();
        $user->assignRole('Teknisi');
        Ticket::factory()->create(['status' => Ticket::STATUS_OPEN]);
        Ticket::factory()->create(['status' => Ticket::STATUS_ASSIGNED_DETECT]);
        Ticket::factory()->create(['status' => Ticket::STATUS_SOLVED_WITH_NOTES]);

        $this->actingAs($user);
        $res = $this->getJson('/api/dashboard/summary');
        $res->assertStatus(200)
            ->assertJson([ 'tickets' => [
                'open' => 1,
                'assigned_detect' => 1,
                'solved_with_notes' => 1,
            ]]);
    }

    /** @test */
    public function technician_can_upload_attachment_to_ticket()
    {
        $tech = User::factory()->create();
        $tech->assignRole('Teknisi');
        $ticket = Ticket::factory()->create();
        $this->actingAs($tech);
        \Illuminate\Support\Facades\Storage::fake('local');

        $file = \Illuminate\Http\UploadedFile::fake()->create('log.txt', 100);
        $response = $this->postJson("/api/tickets/{$ticket->id}/attachments", [
            'file' => $file,
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('attachments', [
            'ticket_id' => $ticket->id,
            'file_name' => 'log.txt',
        ]);

        // fetch ticket and verify attachments are included
        $res2 = $this->getJson("/api/tickets/{$ticket->id}");
        $res2->assertStatus(200);
        $this->assertArrayHasKey('attachments', $res2->json());
    }

    /** @test */
    public function can_attach_file_when_commenting_via_api()
    {
        $tech = User::factory()->create();
        $tech->assignRole('Teknisi');
        $ticket = Ticket::factory()->create();
        $this->actingAs($tech);
        \Illuminate\Support\Facades\Storage::fake('local');

        $file = \Illuminate\Http\UploadedFile::fake()->create('note.pdf', 200);
        $this->postJson("/api/tickets/{$ticket->id}/comments", [
            'message' => 'See attached',
            'attachment' => $file,
        ])->assertStatus(201);

        $this->assertDatabaseHas('attachments', [
            'ticket_id' => $ticket->id,
            'file_name' => 'note.pdf',
        ]);
    }

    /** @test */
    public function web_comment_uploads_attachment()
    {
        $tech = User::factory()->create();
        $tech->assignRole('Teknisi');
        $ticket = Ticket::factory()->create();
        $this->actingAs($tech);
        \Illuminate\Support\Facades\Storage::fake('local');

        $file = \Illuminate\Http\UploadedFile::fake()->create('screenshot.png', 300);
        $response = $this->post("/tickets/{$ticket->id}/comments", [
            'message' => 'Attached screenshot',
            'attachment' => $file,
        ]);
        $response->assertRedirect();

        $this->assertDatabaseHas('attachments', [
            'ticket_id' => $ticket->id,
            'file_name' => 'screenshot.png',
        ]);
    }

    /** @test */
    public function requester_and_technician_can_chat_on_the_same_ticket()
    {
        $requester = User::factory()->create();
        $tech = User::factory()->create();
        $tech->assignRole('Teknisi');

        $ticket = Ticket::factory()->create([
            'requester_id' => $requester->id,
            'assignee_id' => $tech->id,
        ]);

        $this->actingAs($requester)
            ->post("/tickets/{$ticket->id}/comments", [
                'message' => 'Mohon dibantu, perangkat saya masih bermasalah.',
            ])
            ->assertRedirect();

        $this->actingAs($tech)
            ->post("/tickets/{$ticket->id}/comments", [
                'message' => 'Baik, sedang kami cek dan tindak lanjuti.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('ticket_comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $requester->id,
        ]);

        $this->assertDatabaseHas('ticket_comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $tech->id,
        ]);
    }

    /** @test */
    public function unassigned_technician_cannot_comment_on_ticket()
    {
        $requester = User::factory()->create();
        $assignedTech = User::factory()->create();
        $assignedTech->assignRole('Teknisi');

        $unassignedTech = User::factory()->create();
        $unassignedTech->assignRole('Teknisi');

        $ticket = Ticket::factory()->create([
            'requester_id' => $requester->id,
            'assignee_id' => $assignedTech->id,
        ]);

        $this->actingAs($unassignedTech)
            ->post("/tickets/{$ticket->id}/comments", [
                'message' => 'Saya tidak ditugaskan di tiket ini.',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('ticket_comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $unassignedTech->id,
        ]);
    }

    /** @test */
    public function unassigned_technician_can_view_ticket_details()
    {
        $requester = User::factory()->create();
        $assignedTech = User::factory()->create();
        $assignedTech->assignRole('Teknisi');

        $unassignedTech = User::factory()->create();
        $unassignedTech->assignRole('Teknisi');

        $ticket = Ticket::factory()->create([
            'requester_id' => $requester->id,
            'assignee_id' => $assignedTech->id,
        ]);

        $this->actingAs($unassignedTech)
            ->get("/tickets/{$ticket->id}")
            ->assertOk();
    }

    /** @test */
    public function unrelated_user_cannot_comment_on_someone_elses_ticket()
    {
        $requester = User::factory()->create();
        $otherUser = User::factory()->create();

        $ticket = Ticket::factory()->create([
            'requester_id' => $requester->id,
        ]);

        $this->actingAs($otherUser)
            ->post("/tickets/{$ticket->id}/comments", [
                'message' => 'Saya tidak seharusnya bisa ikut komentar di sini.',
            ])
            ->assertForbidden();
    }

    /** @test */
    public function ticket_detail_displays_image_and_pdf_previews_from_attachments()
    {
        $requester = User::factory()->create();
        $ticket = Ticket::factory()->create([
            'requester_id' => $requester->id,
        ]);

        \App\Models\TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $requester->id,
            'message' => 'Berikut screenshot dan dokumen pendukung.',
            'is_internal' => false,
        ]);

        \App\Models\Attachment::create([
            'ticket_id' => $ticket->id,
            'uploader_id' => $requester->id,
            'file_path' => 'attachments/photo-example.png',
            'file_name' => 'photo-example.png',
            'mime_type' => 'image/png',
            'size_bytes' => 1200,
        ]);

        \App\Models\Attachment::create([
            'ticket_id' => $ticket->id,
            'uploader_id' => $requester->id,
            'file_path' => 'attachments/manual.pdf',
            'file_name' => 'manual.pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => 2400,
        ]);

        $response = $this->actingAs($requester)->get(route('tickets.show', $ticket));

        $response->assertOk()
            ->assertSee('Preview Gambar', false)
            ->assertSee('Preview PDF', false)
            ->assertSee('photo-example.png', false)
            ->assertSee('manual.pdf', false);
    }

    /** @test */
    public function web_ticket_attachment_upload_redirects_back_and_saves_file()
    {
        Storage::fake('public');

        $requester = User::factory()->create();
        $ticket = Ticket::factory()->create([
            'requester_id' => $requester->id,
        ]);

        $file = \Illuminate\Http\UploadedFile::fake()->image('evidence.png');

        $this->actingAs($requester)
            ->post("/tickets/{$ticket->id}/attachments", [
                'file' => $file,
            ])
            ->assertRedirect(route('tickets.show', $ticket));

        $attachment = Attachment::query()->where('ticket_id', $ticket->id)->latest('id')->first();

        $this->assertNotNull($attachment);
        Storage::disk('public')->assertExists($attachment->file_path);
    }

    /** @test */
    public function any_user_can_open_uploaded_ticket_attachment_preview()
    {
        Storage::fake('public');

        $requester = User::factory()->create();
        $otherUser = User::factory()->create();
        $ticket = Ticket::factory()->create([
            'requester_id' => $requester->id,
        ]);

        Storage::disk('public')->put('attachments/preview-image.png', 'fake-image-content');

        $attachment = Attachment::create([
            'ticket_id' => $ticket->id,
            'uploader_id' => $requester->id,
            'file_path' => 'attachments/preview-image.png',
            'file_name' => 'preview-image.png',
            'mime_type' => 'image/png',
            'size_bytes' => 18,
        ]);

        // Test that any user (not just the requester) can view the attachment
        $this->actingAs($otherUser)
            ->get(route('tickets.attachments.show', [$ticket, $attachment]))
            ->assertOk();
    }

    /** @test */
    public function regular_user_cannot_edit_someone_elses_ticket()
    {
        $requester = User::factory()->create();
        $otherUser = User::factory()->create();

        $ticket = Ticket::factory()->create([
            'requester_id' => $requester->id,
        ]);

        $this->actingAs($otherUser)
            ->get(route('tickets.edit', $ticket))
            ->assertForbidden();
    }

    /** @test */
    public function regular_user_cannot_update_someone_elses_ticket()
    {
        $requester = User::factory()->create();
        $otherUser = User::factory()->create();

        $ticket = Ticket::factory()->create([
            'requester_id' => $requester->id,
        ]);

        $this->actingAs($otherUser)
            ->patch(route('tickets.update', $ticket), [
                'title' => 'Updated title',
                'description' => 'Updated description',
                'category' => 'IT_SUPPORT',
                'priority' => 'HIGH',
            ])
            ->assertForbidden();
    }

    /** @test */
    public function regular_user_cannot_delete_someone_elses_ticket()
    {
        $requester = User::factory()->create();
        $otherUser = User::factory()->create();

        $ticket = Ticket::factory()->create([
            'requester_id' => $requester->id,
        ]);

        $this->actingAs($otherUser)
            ->delete(route('tickets.destroy', $ticket))
            ->assertForbidden();
    }

    /** @test */
    public function regular_user_can_view_someone_elses_ticket()
    {
        $requester = User::factory()->create();
        $otherUser = User::factory()->create();

        $ticket = Ticket::factory()->create([
            'requester_id' => $requester->id,
        ]);

        $this->actingAs($otherUser)
            ->get(route('tickets.show', $ticket))
            ->assertOk();
    }
}
