<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    protected WhatsAppService $whatsAppService;
    protected EmailService $emailService;

    public function __construct(WhatsAppService $whatsAppService, EmailService $emailService)
    {
        $this->whatsAppService = $whatsAppService;
        $this->emailService = $emailService;
    }

    /**
     * Create notification and send to user
     */
    public function notify(
        User $user,
        string $type,
        string $title,
        string $message,
        ?string $actionType = null,
        ?int $actionId = null,
        bool $sendWhatsApp = true,
        bool $sendEmail = true
    ): Notification {
        // Create system notification
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'action_type' => $actionType,
            'action_id' => $actionId,
        ]);

        // Send WhatsApp if phone number exists
        if ($sendWhatsApp && !empty($user->phone_number)) {
            $this->sendWhatsAppNotification($user, $notification);
        }

        // Send Email if email exists
        if ($sendEmail && !empty($user->email)) {
            $this->sendEmailNotification($user, $notification);
        }

        return $notification;
    }

    /**
     * Send WhatsApp notification
     */
    protected function sendWhatsAppNotification(User $user, Notification $notification): void
    {
        try {
            $response = $this->whatsAppService->send(
                $user->phone_number,
                $notification->message,
                $notification->title
            );

            if ($response['success'] ?? false) {
                $notification->update([
                    'whatsapp_sent' => true,
                    'whatsapp_status' => 'sent',
                    'whatsapp_response' => json_encode($response),
                ]);
            } else {
                $notification->update([
                    'whatsapp_status' => 'failed',
                    'whatsapp_response' => json_encode($response),
                ]);
            }
        } catch (\Exception $e) {
            $notification->update([
                'whatsapp_status' => 'failed',
                'whatsapp_response' => json_encode(['error' => $e->getMessage()]),
            ]);
        }
    }

    /**
     * Send Email notification
     */
    protected function sendEmailNotification(User $user, Notification $notification): void
    {
        try {
            $response = $this->emailService->send(
                $user->email,
                $notification->title,
                $notification->message
            );

            if ($response['success'] ?? false) {
                $notification->update([
                    'email_sent' => true,
                    'email_status' => 'sent',
                    'email_response' => json_encode($response),
                ]);
            } else {
                $notification->update([
                    'email_status' => 'failed',
                    'email_response' => json_encode($response),
                ]);
            }
        } catch (\Exception $e) {
            $notification->update([
                'email_status' => 'failed',
                'email_response' => json_encode(['error' => $e->getMessage()]),
            ]);
        }
    }

    /**
     * Create ticket notification
     */
    public function notifyTicketCreated(User $user, $ticket, bool $sendWhatsApp = true, bool $sendEmail = true): Notification
    {
        return $this->notify(
            $user,
            'success',
            '📋 Tiket Baru Dibuat',
            "Tiket '{$ticket->title}' telah dibuat.",
            'ticket',
            $ticket->id,
            $sendWhatsApp,
            $sendEmail
        );
    }

    /**
     * Create ticket status update notification
     */
    public function notifyTicketUpdated(User $user, $ticket, string $oldStatus, bool $sendWhatsApp = true, bool $sendEmail = true): Notification
    {
        return $this->notify(
            $user,
            'info',
            '🔄 Tiket Diperbarui',
            "Tiket '{$ticket->code}' status berubah dari {$oldStatus} menjadi {$ticket->status}.",
            'ticket',
            $ticket->id,
            $sendWhatsApp,
            $sendEmail
        );
    }

    /**
     * Create ticket resolved notification
     */
    public function notifyTicketResolved(User $user, $ticket, bool $sendWhatsApp = true, bool $sendEmail = true): Notification
    {
        return $this->notify(
            $user,
            'success',
            '✅ Tiket Diselesaikan',
            "Tiket '{$ticket->code}' telah diselesaikan.",
            'ticket',
            $ticket->id,
            $sendWhatsApp,
            $sendEmail
        );
    }

    /**
     * Notify all admins
     */
    public function notifyAdmins(string $type, string $title, string $message, ?string $actionType = null, ?int $actionId = null, bool $sendWhatsApp = true, bool $sendEmail = true): void
    {
        $admins = User::role('Admin')->get();

        foreach ($admins as $admin) {
            $this->notify($admin, $type, $title, $message, $actionType, $actionId, $sendWhatsApp, $sendEmail);
        }
    }

    /**
     * Notify technician assigned to a ticket
     */
    public function notifyTicketAssigned(User $assignee, $ticket, bool $sendWhatsApp = true, bool $sendEmail = true): Notification
    {
        return $this->notify(
            $assignee,
            'info',
            '🛠️ Tiket Ditugaskan',
            "Anda ditugaskan menangani tiket {$ticket->code}.",
            'ticket',
            $ticket->id,
            $sendWhatsApp,
            $sendEmail
        );
    }

    /**
     * Notify requester that a technician has been assigned
     */
    public function notifyTicketAssignedToRequester(User $requester, $ticket, bool $sendWhatsApp = true, bool $sendEmail = true): Notification
    {
        return $this->notify(
            $requester,
            'info',
            '👤 Teknisi Ditugaskan',
            "Tiket {$ticket->code} telah ditugaskan ke teknisi.",
            'ticket',
            $ticket->id,
            $sendWhatsApp,
            $sendEmail
        );
    }

    /**
     * Notify user about a new ticket comment
     */
    public function notifyTicketCommented(User $recipient, $ticket, $comment, bool $sendWhatsApp = true, bool $sendEmail = true): Notification
    {
        return $this->notify(
            $recipient,
            'info',
            '💬 Komentar Baru pada Tiket',
            "Komentar baru pada tiket {$ticket->code}: {$comment->message}",
            'ticket',
            $ticket->id,
            $sendWhatsApp,
            $sendEmail
        );
    }

    /**
     * Notify admins about a newly created ticket
     */
    public function notifyAdminsTicketCreated($ticket): void
    {
        $this->notifyAdmins(
            'success',
            '📌 Permintaan Baru Masuk',
            "Tiket baru {$ticket->code} telah diajukan oleh {$ticket->requester->name}.",
            'ticket',
            $ticket->id
        );
    }

    /**
     * Notify admins about a ticket resolution
     */
    public function notifyAdminsTicketResolved($ticket): void
    {
        $this->notifyAdmins(
            'success',
            '✅ Permintaan Selesai',
            "Tiket {$ticket->code} telah diselesaikan.",
            'ticket',
            $ticket->id
        );
    }

    /**
     * Notify admins about a reservation request
     */
    public function notifyAdminsReservationCreated($reservation): void
    {
        $this->notifyAdmins(
            'success',
            '📌 Pengajuan Zoom Baru',
            "Pengajuan Zoom {$reservation->code} oleh {$reservation->requester->name} telah dibuat.",
            'reservation',
            $reservation->id
        );
    }

    /**
     * Notify admins about a reservation completion
     */
    public function notifyAdminsReservationCompleted($reservation): void
    {
        $this->notifyAdmins(
            'success',
            '✅ Pengajuan Zoom Selesai',
            "Pengajuan Zoom {$reservation->code} telah selesai.",
            'reservation',
            $reservation->id
        );
    }

    /**
     * Notify technician assigned to a reservation
     */
    public function notifyReservationAssigned(User $approver, $reservation, bool $sendWhatsApp = true, bool $sendEmail = true): Notification
    {
        return $this->notify(
            $approver,
            'info',
            '🛠️ Tugas Zoom Meeting Ditugaskan',
            "Anda ditugaskan sebagai teknisi untuk pengajuan Zoom {$reservation->code}.",
            'reservation',
            $reservation->id,
            $sendWhatsApp,
            $sendEmail
        );
    }

    /**
     * Notify requester when Zoom link becomes available
     */
    public function notifyReservationZoomLinkAvailable(User $requester, $reservation, bool $sendWhatsApp = true, bool $sendEmail = true): Notification
    {
        return $this->notify(
            $requester,
            'success',
            '🔗 Link Zoom Tersedia',
            "Link Zoom untuk pengajuan {$reservation->code} telah tersedia: {$reservation->zoom_link}",
            'reservation',
            $reservation->id,
            $sendWhatsApp,
            $sendEmail
        );
    }

    /**
     * Notify requester when reservation is completed
     */
    public function notifyReservationCompleted(User $user, $reservation, bool $sendWhatsApp = true, bool $sendEmail = true): Notification
    {
        return $this->notify(
            $user,
            'success',
            '✅ Pengajuan Zoom Selesai',
            "Pengajuan Zoom {$reservation->code} telah selesai.",
            'reservation',
            $reservation->id,
            $sendWhatsApp,
            $sendEmail
        );
    }

    /**
     * Create reservation notification
     */
    public function notifyReservationCreated(User $user, $reservation, bool $sendWhatsApp = true, bool $sendEmail = true): Notification
    {
        return $this->notify(
            $user,
            'info',
            '🏢 Pengajuan Zoom Baru',
            "Pengajuan Zoom '{$reservation->room_name}' pada {$reservation->start_time->format('d/m/Y H:i')} telah dibuat.",
            'reservation',
            $reservation->id,
            $sendWhatsApp,
            $sendEmail
        );
    }

    /**
     * Create reservation approved notification
     */
    public function notifyReservationApproved(User $user, $reservation, bool $sendWhatsApp = true, bool $sendEmail = true): Notification
    {
        return $this->notify(
            $user,
            'success',
            '✅ Pengajuan Zoom Ditindaklanjuti',
            "Pengajuan Zoom '{$reservation->room_name}' telah disetujui." . ($reservation->zoom_link ? " Link: {$reservation->zoom_link}" : ''),
            'reservation',
            $reservation->id,
            $sendWhatsApp,
            $sendEmail
        );
    }

    /**
     * Create asset notification
     */
    public function notifyAssetCreated(User $user, $asset, bool $sendWhatsApp = true, bool $sendEmail = true): Notification
    {
        return $this->notify(
            $user,
            'info',
            '📦 Aset Baru Ditambahkan',
            "Aset '{$asset->name}' (Kode: {$asset->asset_code}) telah ditambahkan ke sistem.",
            'asset',
            $asset->id,
            $sendWhatsApp,
            $sendEmail
        );
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId): void
    {
        $notification = Notification::find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    /**
     * Mark all user notifications as read
     */
    public function markAllAsRead(User $user): void
    {
        Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    /**
     * Get user's unread notifications
     */
    public function getUnreadNotifications(User $user, int $limit = 10)
    {
        return Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get user's all notifications
     */
    public function getNotifications(User $user, int $limit = 20)
    {
        return Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
