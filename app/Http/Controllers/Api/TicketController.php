<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Models\Ticket;
use App\Models\Log;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
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

        return $q->paginate(15);
    }

    public function show(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        if (! $user->hasAnyRole(['Admin', 'Teknisi']) && $ticket->requester_id !== $user->id) {
            abort(403);
        }

        return $ticket->load(['requester', 'assignee', 'asset', 'comments.user', 'comments.attachments', 'attachments.uploader']);
    }

    public function store(StoreTicketRequest $request)
    {
        $user = $request->user();

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

        Log::create([
            'actor_id' => $user->id,
            'entity_type' => 'TICKET',
            'entity_id' => $ticket->id,
            'action' => 'CREATED',
            'meta' => ['code' => $ticket->code],
        ]);

        return response()->json($ticket->load(['requester', 'assignee', 'asset']), 201);
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
        $ticket->save();

        Log::create([
            'actor_id' => $user->id,
            'entity_type' => 'TICKET',
            'entity_id' => $ticket->id,
            'action' => 'STATUS_CHANGED',
            'meta' => [
                'status' => $ticket->status,
                'assignee_id' => $ticket->assignee_id,
            ],
        ]);

        // send notifications
        $old = $oldStatus;
        if ($ticket->requester) {
            $this->notificationService->notifyTicketUpdated($ticket->requester, $ticket, $old, false, false);
        }
        if ($ticket->assignee && $ticket->assignee->id !== $ticket->requester_id) {
            $this->notificationService->notifyTicketUpdated($ticket->assignee, $ticket, $old, false, false);
        }

        return response()->json($ticket->fresh()->load(['requester', 'assignee', 'asset']));
    }

}

