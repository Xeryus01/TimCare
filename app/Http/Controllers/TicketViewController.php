<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Asset;
use App\Models\Attachment;
use App\Models\TicketComment;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Log;

class TicketViewController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $q = Ticket::query()->with(['requester', 'assignee', 'asset'])->latest();

        if (! $user->hasAnyRole(['Admin', 'Teknisi'])) {
            $q->where('requester_id', $user->id);
        }

        // allow filtering by status, priority or assignee for all users
        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }
        if ($request->filled('assignee_id')) {
            $q->where('assignee_id', $request->assignee_id);
        }

        $perPage = $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50]) ? (int)$perPage : 10;
        
        $tickets = $q->paginate($perPage)->appends(request()->query());
        return view('tickets.index', compact('tickets'));
    }

    public function create(Request $request)
    {
        $assets = \Cache::remember('assets.all', 3600, function () {
            return Asset::all();
        });
        return view('tickets.create', compact('assets'));
    }

    public function store(StoreTicketRequest $request)
    {
        $data = $request->validated();
        $data['code'] = Ticket::generateCode();
        $data['requester_id'] = $request->user()->id;
        $data['status'] = Ticket::STATUS_OPEN;

        $ticket = Ticket::create($data);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('attachments', 'public');
            Attachment::create([
                'ticket_id' => $ticket->id,
                'uploader_id' => $request->user()->id,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'size_bytes' => $file->getSize(),
            ]);
        }

        Log::create([
            'actor_id' => $request->user()->id,
            'entity_type' => 'TICKET',
            'entity_id' => $ticket->id,
            'action' => 'CREATED',
            'meta' => ['code' => $ticket->code],
            'created_at' => now(),
        ]);

        // Send notification
        $this->notificationService->notifyTicketCreated($request->user(), $ticket);

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket created');
    }

    public function show(Request $request, Ticket $ticket)
    {
        $ticket->load('requester', 'assignee', 'asset', 'comments.user', 'comments.attachments', 'attachments.uploader');

        $technicians = \App\Models\User::role('Teknisi')->get();

        return view('tickets.show', compact('ticket', 'technicians'));
    }

    public function edit(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        if (! $user->hasAnyRole(['Admin', 'Teknisi']) && $ticket->requester_id !== $user->id) {
            abort(403);
        }

        if ($ticket->status === Ticket::STATUS_CANCELLED && ! $user->hasRole('Admin')) {
            abort(403, 'Tiket yang dibatalkan tidak dapat diedit kembali kecuali oleh Admin.');
        }

        $assets = Asset::all();
        $technicians = \App\Models\User::role('Teknisi')->get();
        return view('tickets.edit', compact('ticket', 'assets', 'technicians'));
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        $user = $request->user();

        if (! $user->hasAnyRole(['Admin', 'Teknisi']) && $ticket->requester_id !== $user->id) {
            abort(403);
        }

        if ($ticket->status === Ticket::STATUS_CANCELLED && ! $user->hasRole('Admin')) {
            abort(403, 'Tiket yang dibatalkan hanya dapat diedit oleh Admin.');
        }

        $data = $request->validated();

        // Only Admin can assign tickets
        if (isset($data['assignee_id']) && ! $user->hasRole('Admin')) {
            abort(403, 'Hanya Admin yang dapat menugaskan petugas.');
        }

        // Allow users to cancel their own tickets
        if (isset($data['status']) && $data['status'] === Ticket::STATUS_CANCELLED && $ticket->requester_id === $user->id) {
            // User is cancelling their own ticket - allow it
        } elseif (isset($data['status']) && ! $user->hasAnyRole(['Admin', 'Teknisi'])) {
            // Regular users cannot change status except to cancel
            unset($data['status']);
        }

        $oldStatus = $ticket->status;

        // Automatic status changes for tickets
        // Jika petugas diassign dan status belum ASSIGNED_DETECT, ubah otomatis
        if (isset($data['assignee_id']) && $data['assignee_id'] !== null) {
            $completedStatuses = [Ticket::STATUS_SOLVED, Ticket::STATUS_SOLVED_WITH_NOTES, Ticket::STATUS_REJECTED, Ticket::STATUS_CANCELLED];
            if (!in_array($ticket->status, $completedStatuses, true) && $ticket->status !== Ticket::STATUS_ASSIGNED_DETECT) {
                $data['status'] = Ticket::STATUS_ASSIGNED_DETECT;
            }
        }

        if (isset($data['status']) && $data['status'] === Ticket::STATUS_CANCELLED) {
            // User is cancelling the ticket
            $data['status'] = Ticket::STATUS_CANCELLED;
        }

        $ticket->fill($data);
        if (isset($data['status']) && in_array($data['status'], [Ticket::STATUS_SOLVED, Ticket::STATUS_SOLVED_WITH_NOTES], true)) {
            $ticket->resolved_at = now();
        }
        $ticket->save();

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('attachments', 'public');
            Attachment::create([
                'ticket_id' => $ticket->id,
                'uploader_id' => $request->user()->id,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'size_bytes' => $file->getSize(),
            ]);
        }

        // Send notification if status changed
        if ($oldStatus !== $ticket->status) {
            if (in_array($ticket->status, [Ticket::STATUS_SOLVED, Ticket::STATUS_SOLVED_WITH_NOTES], true)) {
                $this->notificationService->notifyTicketResolved($ticket->assignee ?? $ticket->requester, $ticket);
            } else {
                $this->notificationService->notifyTicketUpdated($ticket->assignee ?? $ticket->requester, $ticket, $oldStatus);
            }
        }

        return redirect()->route('tickets.show', $ticket)->with('success','Ticket updated');
    }

    public function destroy(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        if (! $user->hasAnyRole(['Admin', 'Teknisi']) && $ticket->requester_id !== $user->id) {
            abort(403);
        }

        $ticket->delete();
        return redirect()->route('tickets.index')->with('success','Ticket deleted');
    }

    public function comment(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        if (! $user->hasAnyRole(['Admin', 'Teknisi']) && $ticket->requester_id !== $user->id) {
            abort(403);
        }

        $data = $request->validate([
            'message' => 'required|string',
            'status' => [
                'nullable',
                'string',
                Rule::in(array_merge([''], Ticket::statuses())),
            ],
            'attachment' => 'sometimes|file|max:10240',
        ]);

        // Create comment with only the fields that belong to TicketComment
        $commentData = [
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $data['message'],
            'is_internal' => false, // Regular users can't create internal comments via web form
        ];

        $comment = TicketComment::create($commentData);

        // handle optional file
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('attachments', 'public');
            Attachment::create([
                'ticket_id' => $ticket->id,
                'comment_id' => $comment->id,
                'uploader_id' => $request->user()->id,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'size_bytes' => $file->getSize(),
            ]);
        }

        // if status was provided and user is technician/admin, update ticket
        if (isset($data['status']) && $request->user()->hasAnyRole(['Admin','Teknisi'])) {
            $oldStatus = $ticket->status;
            $ticket->status = $data['status'];
            if (in_array($ticket->status, [Ticket::STATUS_SOLVED, Ticket::STATUS_SOLVED_WITH_NOTES], true)) {
                $ticket->resolved_at = now();
            }
            $ticket->save();

            // Log status change
            Log::create([
                'actor_id' => $user->id,
                'entity_type' => 'TICKET',
                'entity_id' => $ticket->id,
                'action' => 'STATUS_CHANGED',
                'meta' => ['status' => $ticket->status],
                'created_at' => now(),
            ]);

            // Send notification if status changed
            if ($oldStatus !== $ticket->status) {
                if (in_array($ticket->status, [Ticket::STATUS_SOLVED, Ticket::STATUS_SOLVED_WITH_NOTES], true)) {
                    $this->notificationService->notifyTicketResolved($ticket->assignee ?? $ticket->requester, $ticket);
                } else {
                    $this->notificationService->notifyTicketUpdated($ticket->assignee ?? $ticket->requester, $ticket, $oldStatus);
                }
            }
        }

        // Log comment creation
        Log::create([
            'actor_id' => $user->id,
            'entity_type' => 'TICKET',
            'entity_id' => $ticket->id,
            'action' => 'COMMENTED',
            'meta' => ['comment_id' => $comment->id],
            'created_at' => now(),
        ]);

        // Send notification about new comment
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

        return redirect()->route('tickets.show', $ticket)->with('success','Pesan berhasil dikirim');
    }

    public function uploadAttachment(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        if (! $user->hasAnyRole(['Admin', 'Teknisi']) && $ticket->requester_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'file' => 'required|file|max:10240|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,csv,txt',
        ]);

        $file = $validated['file'];
        $path = $file->store('attachments', 'public');

        Attachment::create([
            'ticket_id' => $ticket->id,
            'uploader_id' => $user->id,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size_bytes' => $file->getSize(),
        ]);

        return redirect()->route('tickets.show', $ticket)->with('success', 'Lampiran berhasil diunggah.');
    }

    public function showAttachment(Request $request, Ticket $ticket, Attachment $attachment)
    {
        $user = $request->user();

        if ((int) $attachment->ticket_id !== (int) $ticket->id) {
            abort(404);
        }

        $diskName = Storage::disk('public')->exists($attachment->file_path) ? 'public' : 'local';
        $disk = Storage::disk($diskName);

        if (! $disk->exists($attachment->file_path)) {
            abort(404);
        }

        return $disk->response($attachment->file_path, $attachment->file_name, [
            'Content-Type' => $attachment->mime_type ?: ($disk->mimeType($attachment->file_path) ?: 'application/octet-stream'),
            'Content-Disposition' => 'inline; filename="' . $attachment->file_name . '"',
        ]);
    }
}
