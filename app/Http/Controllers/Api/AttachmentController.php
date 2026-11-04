<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttachmentResource;
use App\Models\Attachment;
use App\Models\Ticket;
use App\Services\AuditTrailService;
use App\Services\SecureFileUploadService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttachmentController extends Controller
{
    public function __construct(
        private SecureFileUploadService $fileUploadService,
        private AuditTrailService $auditTrailService
    ) {}

    public function store(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        if (! $user->hasRole('Admin')
            && $ticket->requester_id !== $user->id
            && ! ($user->hasRole('Teknisi') && $ticket->assignee_id === $user->id)) {
            abort(403, 'Anda tidak memiliki akses untuk menambahkan lampiran pada tiket ini.');
        }

        $validated = $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,txt|max:' . SecureFileUploadService::MAX_ATTACHMENT_KB,
            'comment_id' => [
                'nullable',
                Rule::exists('ticket_comments', 'id')
                    ->where(fn ($query) => $query->where('ticket_id', $ticket->id)),
            ],
        ]);

        $fileInfo = $this->fileUploadService->storeTicketAttachment($validated['file']);

        $attachment = Attachment::create([
            'ticket_id' => $ticket->id,
            'comment_id' => $validated['comment_id'] ?? null,
            'uploader_id' => $user->id,
            'file_path' => $fileInfo['path'],
            'file_name' => $fileInfo['original_name'],
            'mime_type' => $fileInfo['mime_type'],
            'size_bytes' => $fileInfo['size_bytes'],
        ]);

        $this->auditTrailService->record($request, $user->id, 'Attachment', $attachment->id, 'CREATED', [
            'ticket_id' => $ticket->id,
            'comment_id' => $attachment->comment_id,
            'file_name' => $attachment->file_name,
            'mime_type' => $attachment->mime_type,
            'size_bytes' => $attachment->size_bytes,
        ]);

        return (new AttachmentResource($attachment->load('uploader')))->response()->setStatusCode(201);
    }
}
