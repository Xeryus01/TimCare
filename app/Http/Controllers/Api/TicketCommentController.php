<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketCommentResource;
use App\Models\Attachment;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Services\AuditTrailService;
use App\Services\NotificationService;
use App\Services\SecureFileUploadService;
use Illuminate\Http\Request;

class TicketCommentController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(
        NotificationService $notificationService,
        private SecureFileUploadService $fileUploadService,
        private AuditTrailService $auditTrailService
    )
    {
        $this->notificationService = $notificationService;
    }

    public function store(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'is_internal' => 'sometimes|boolean',
            // allow status change when technicians/admins comment
            'status' => 'sometimes|string|in:' . implode(',', Ticket::statuses()),
            'attachment' => 'sometimes|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,txt|max:' . SecureFileUploadService::MAX_ATTACHMENT_KB,
        ]);

        $user = $request->user();

        if (! $user->hasRole('Admin')
            && $ticket->requester_id !== $user->id
            && ! ($user->hasRole('Teknisi') && $ticket->assignee_id === $user->id)) {
            abort(403, 'Anda tidak memiliki akses untuk mengomentari tiket ini.');
        }

        // Normal user cannot create internal notes
        $isInternal = (bool) ($validated['is_internal'] ?? false);
        if (! $user->hasAnyRole(['Admin', 'Teknisi'])) {
            $isInternal = false;
        }

        $comment = TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $validated['message'],
            'is_internal' => $isInternal,
        ]);

        // handle optional attachment
        if (isset($validated['attachment'])) {
            $fileInfo = $this->fileUploadService->storeTicketAttachment($validated['attachment']);
            Attachment::create([
                'ticket_id' => $ticket->id,
                'comment_id' => $comment->id,
                'uploader_id' => $user->id,
                'file_path' => $fileInfo['path'],
                'file_name' => $fileInfo['original_name'],
                'mime_type' => $fileInfo['mime_type'],
                'size_bytes' => $fileInfo['size_bytes'],
            ]);
        }

        // if status change requested
        if (isset($validated['status']) && $user->hasAnyRole(['Admin', 'Teknisi'])) {
            $ticket->status = $validated['status'];
            if (in_array($ticket->status, [Ticket::STATUS_SOLVED, Ticket::STATUS_SOLVED_WITH_NOTES], true)) {
                $ticket->resolved_at = now();
            }
            $ticket->save();

            $this->auditTrailService->record($request, $user->id, 'TICKET', $ticket->id, 'STATUS_CHANGED', [
                'after' => ['status' => $ticket->status],
                'source' => 'comment',
            ]);
        }

        $this->auditTrailService->record($request, $user->id, 'TICKET', $ticket->id, 'COMMENTED', [
            'comment_id' => $comment->id,
            'is_internal' => $comment->is_internal,
            'has_attachment' => $request->hasFile('attachment'),
        ]);

        // notify requester and assignee via custom notification service
        if ($ticket->requester && $ticket->requester->id !== $user->id) {
            $this->notificationService->notify(
                $ticket->requester,
                'info',
                '💬 Komentar Baru pada Tiket',
                "Komentar baru pada tiket {$ticket->code}: {$comment->message}",
                'ticket',
                $ticket->id,
                false,
                false
            );
        }

        if ($ticket->assignee && $ticket->assignee->id !== $user->id) {
            $this->notificationService->notify(
                $ticket->assignee,
                'info',
                '💬 Komentar Baru pada Tiket',
                "Komentar baru pada tiket {$ticket->code}: {$comment->message}",
                'ticket',
                $ticket->id,
                false,
                false
            );
        }

        return (new TicketCommentResource($comment->load(['user', 'attachments'])))->response()->setStatusCode(201);
    }
}
