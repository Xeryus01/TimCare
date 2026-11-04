<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Attachment;
use App\Models\Ticket;
use App\Services\AuditTrailService;
use App\Services\NotificationService;
use App\Services\SecureFileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
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
    public function index(Request $request)
    {
        $user = $request->user();

        $q = Ticket::query()
            ->with(['requester', 'assignee', 'asset'])
            ->latest();

        if ($request->filled('status')) {
            $q->whereIn('status', Ticket::resolveStatusFilter($request->status));
        }
        if ($request->filled('priority')) {
            $q->where('priority', $request->priority);
        }
        if ($request->filled('assignee_id')) {
            $q->where('assignee_id', $request->assignee_id);
        }

        // Admin/Teknisi see all; User only sees own
        if (! $user->hasAnyRole(['Admin', 'Teknisi'])) {
            $q->where('requester_id', $user->id);
        }

        return TicketResource::collection($q->paginate(15));
    }

    public function show(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        if (! $user->hasAnyRole(['Admin', 'Teknisi']) && $ticket->requester_id !== $user->id) {
            abort(403);
        }

        $canSeeInternalComments = $user->hasAnyRole(['Admin', 'Teknisi']);

        $ticket->load([
            'requester',
            'assignee',
            'asset',
            'comments' => function ($query) use ($canSeeInternalComments) {
                $query
                    ->when(! $canSeeInternalComments, fn ($comments) => $comments->where('is_internal', false))
                    ->with(['user', 'attachments']);
            },
            'attachments.uploader',
        ]);

        return new TicketResource($ticket);
    }

    public function store(StoreTicketRequest $request)
    {
        $user = $request->user();

        $ticket = DB::transaction(function () use ($request, $user) {
            $ticket = Ticket::create([
                'code' => Ticket::generateCode(),
                'requester_id' => $user->id,
                'asset_id' => $request->asset_id,
                'category' => $request->category,
                'title' => $request->title,
                'description' => $request->description,
                'priority' => $request->priority,
                'status' => Ticket::STATUS_OPEN,
            ]);

            if ($request->hasFile('attachment')) {
                $fileInfo = $this->fileUploadService->storeTicketAttachment(
                    $request->file('attachment'),
                    'attachments',
                    SecureFileUploadService::MAX_INITIAL_ATTACHMENT_KB
                );

                Attachment::create([
                    'ticket_id' => $ticket->id,
                    'uploader_id' => $user->id,
                    'file_path' => $fileInfo['path'],
                    'file_name' => $fileInfo['original_name'],
                    'mime_type' => $fileInfo['mime_type'],
                    'size_bytes' => $fileInfo['size_bytes'],
                ]);
            }

            $this->auditTrailService->record($request, $user->id, 'TICKET', $ticket->id, 'CREATED', [
                'code' => $ticket->code,
                'category' => $ticket->category,
                'priority' => $ticket->priority,
                'asset_id' => $ticket->asset_id,
            ]);

            return $ticket;
        });

        return (new TicketResource($ticket->load(['requester', 'assignee', 'asset', 'attachments.uploader'])))->response()->setStatusCode(201);
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $user = $request->user();
        if (! $user->hasAnyRole(['Admin', 'Teknisi'])) {
            abort(403);
        }

        $validated = $request->validate([
            // only allow statuses defined in the model
            'status' => 'required|in:' . implode(',', Ticket::statuses()),
            'assignee_id' => 'nullable|exists:users,id',
        ]);

        // Only Admin can assign tickets
        if (isset($validated['assignee_id']) && ! $user->hasRole('Admin')) {
            abort(403, 'Hanya Admin yang dapat menugaskan petugas.');
        }

        $oldStatus = $ticket->status;

        $ticket->status = $validated['status'];

        // Tiket yang dibatalkan tidak boleh memiliki penugasan teknisi.
        // Jika status diubah menjadi "Batal", hapus penugasan yang ada dan
        // abaikan upaya penugasan teknisi pada request ini.
        if ($ticket->status === Ticket::STATUS_CANCELLED) {
            $validated['assignee_id'] = null;
            $ticket->assignee_id = null;
        } else {
            $ticket->assignee_id = $validated['assignee_id'] ?? $ticket->assignee_id;
        }
        
        // Automatic status change when assigning petugas
        if (isset($validated['assignee_id']) && $validated['assignee_id'] !== null) {
            $completedStatuses = [Ticket::STATUS_SOLVED, Ticket::STATUS_SOLVED_WITH_NOTES, Ticket::STATUS_REJECTED, Ticket::STATUS_CANCELLED];
            if (!in_array($ticket->status, $completedStatuses, true) && $ticket->status !== Ticket::STATUS_ASSIGNED_DETECT) {
                $ticket->status = Ticket::STATUS_ASSIGNED_DETECT;
            }
        }
        
        if (in_array($ticket->status, [Ticket::STATUS_SOLVED, Ticket::STATUS_SOLVED_WITH_NOTES], true)) {
            $ticket->resolved_at = now();
        }
        DB::transaction(fn () => $ticket->save());

        $this->auditTrailService->record($request, $user->id, 'TICKET', $ticket->id, 'STATUS_CHANGED', [
            'before' => ['status' => $oldStatus],
            'after' => ['status' => $ticket->status, 'assignee_id' => $ticket->assignee_id],
        ]);

        // send notifications
        $old = $oldStatus;
        if ($ticket->requester) {
            $this->notificationService->notifyTicketUpdated($ticket->requester, $ticket, $old, false, false);
        }
        if ($ticket->assignee && $ticket->assignee->id !== $ticket->requester_id) {
            $this->notificationService->notifyTicketUpdated($ticket->assignee, $ticket, $old, false, false);
        }

        return new TicketResource($ticket->fresh()->load(['requester', 'assignee', 'asset']));
    }

}
