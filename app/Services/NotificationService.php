<?php

namespace App\Services;

use App\Jobs\SendNotificationDelivery;
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
     * Create notification and send to user.
     *
     * Kebijakan pengiriman:
     * - Notifikasi sistem (in-app) SELALU dibuat.
     * - WhatsApp hanya dikirim bila $sendWhatsApp = true DAN user punya nomor HP.
     *   Kapan WA dikirim diatur di tiap helper notify* sesuai aturan:
     *     1) Tiket/Zoom baru        -> WA ke ADMIN
     *     2) Penugasan ke teknisi   -> WA ke TEKNISI yang ditugaskan + PENGAJU
     *     3) Link Zoom siap         -> WA ke PENGAJU (beserta link)
     *     4) Tiket/Zoom selesai     -> WA ke PENGAJU + TEKNISI
     * - Email dikirim bila $sendEmail = true DAN user punya email.
     * - Pengiriman berjalan SETELAH respons HTTP (dispatchAfterResponse), tanpa
     *   queue worker, sehingga cocok untuk hosting cPanel biasa.
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

        // Kirim WA/Email sesuai flag & ketersediaan kontak user.
        $doWhatsApp = $sendWhatsApp && !empty($user->phone_number);
        $doEmail = $sendEmail && !empty($user->email);

        if ($doWhatsApp || $doEmail) {
            SendNotificationDelivery::dispatchAfterResponse($notification->id, $doWhatsApp, $doEmail);
        }

        return $notification;
    }

    /**
     * Send WhatsApp notification
     */
    public function sendWhatsAppNotification(User $user, Notification $notification): void
    {
        try {
            // Sisipkan link langsung menuju tiket/permintaan Zoom ke isi pesan WA.
            $message = $notification->message;
            $actionUrl = $this->buildActionUrl($notification->action_type, $notification->action_id);
            if ($actionUrl) {
                $message .= "\n\n" . $this->actionLabel($notification->action_type) . ': ' . $actionUrl;
            }

            $response = $this->whatsAppService->send(
                $user->phone_number,
                $message,
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
    public function sendEmailNotification(User $user, Notification $notification): void
    {
        try {
            // Sertakan tombol/link langsung menuju tiket/permintaan Zoom di email.
            $actionUrl = $this->buildActionUrl($notification->action_type, $notification->action_id);
            $actionText = $actionUrl ? $this->actionLabel($notification->action_type) : null;

            $response = $this->emailService->send(
                $user->email,
                $notification->title,
                $notification->message,
                $actionUrl,
                $actionText
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
     * Bangun URL absolut menuju detail entitas terkait notifikasi
     * (tiket / permintaan Zoom / aset) berdasarkan action_type & action_id.
     * Mengembalikan null bila tipe tidak dikenal atau route tidak tersedia.
     * Catatan: URL memakai APP_URL, jadi pastikan APP_URL di .env sudah benar
     * (mis. https://domain-anda) agar link dapat diklik dari WA/email.
     */
    private function buildActionUrl(?string $actionType, ?int $actionId): ?string
    {
        if (empty($actionType) || empty($actionId)) {
            return null;
        }

        $routeName = match ($actionType) {
            'ticket' => 'tickets.show',
            'reservation' => 'reservations.show',
            'asset' => 'assets.show',
            default => null,
        };

        if ($routeName === null) {
            return null;
        }

        try {
            return route($routeName, $actionId);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Label tombol/teks link sesuai jenis entitas.
     */
    private function actionLabel(?string $actionType): string
    {
        return match ($actionType) {
            'ticket' => 'Buka Tiket',
            'reservation' => 'Buka Permintaan Zoom',
            'asset' => 'Buka Aset',
            default => 'Buka di TimCare',
        };
    }

    /**
     * Create ticket notification (untuk PENGAJU saat tiket dibuat).
     * WA TIDAK dikirim ke pengaju di sini -- WA tiket baru hanya ke admin.
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
            false,
            $sendEmail
        );
    }

    /**
     * Create ticket status update notification.
     * Perubahan status di luar 4 kondisi WA -> WA tidak dikirim.
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
            false,
            $sendEmail
        );
    }

    /**
     * Create ticket resolved notification.
     * Tiket selesai -> WA ke penerima (dipakai untuk PENGAJU & TEKNISI).
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
            true,
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
     * Notify technician assigned to a ticket.
     * Penugasan -> WA ke teknisi yang ditugaskan.
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
            true,
            $sendEmail
        );
    }

    /**
     * Notify requester that a technician has been assigned.
     * Penugasan -> WA ke pengaju tiket.
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
            true,
            $sendEmail
        );
    }

    /**
     * Notify user about a new ticket comment.
     * Komentar di luar 4 kondisi WA -> WA tidak dikirim.
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
            false,
            $sendEmail
        );
    }

    /**
     * Notify admins about a newly created ticket.
     * Tiket baru -> WA ke ADMIN.
     */
    public function notifyAdminsTicketCreated($ticket): void
    {
        $this->notifyAdmins(
            'success',
            '📌 Permintaan Baru Masuk',
            "Tiket baru {$ticket->code} telah diajukan oleh {$ticket->requester->name}.",
            'ticket',
            $ticket->id,
            true,
            true
        );
    }

    /**
     * Notify admins about a ticket resolution.
     * Tiket selesai -> admin TIDAK menerima WA (hanya pengaju & teknisi).
     */
    public function notifyAdminsTicketResolved($ticket): void
    {
        $this->notifyAdmins(
            'success',
            '✅ Permintaan Selesai',
            "Tiket {$ticket->code} telah diselesaikan.",
            'ticket',
            $ticket->id,
            false,
            true
        );
    }

    /**
     * Notify admins about a reservation request.
     * Zoom baru -> WA ke ADMIN.
     */
    public function notifyAdminsReservationCreated($reservation): void
    {
        $this->notifyAdmins(
            'success',
            '📌 Pengajuan Zoom Baru',
            "Pengajuan Zoom {$reservation->code} oleh {$reservation->requester->name} telah dibuat.",
            'reservation',
            $reservation->id,
            true,
            true
        );
    }

    /**
     * Notify admins about a reservation completion.
     * Zoom selesai -> admin TIDAK menerima WA (hanya pengaju & teknisi).
     */
    public function notifyAdminsReservationCompleted($reservation): void
    {
        $this->notifyAdmins(
            'success',
            '✅ Pengajuan Zoom Selesai',
            "Pengajuan Zoom {$reservation->code} telah selesai.",
            'reservation',
            $reservation->id,
            false,
            true
        );
    }

    /**
     * Notify technician assigned to a reservation.
     * Penugasan -> WA ke teknisi (petugas) yang ditugaskan.
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
            true,
            $sendEmail
        );
    }

    /**
     * Notify requester when Zoom link becomes available.
     * Link Zoom siap -> WA ke PENGAJU beserta link Zoom.
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
            true,
            $sendEmail
        );
    }

    /**
     * Notify requester when reservation is completed.
     * Zoom selesai -> WA ke PENGAJU.
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
            true,
            $sendEmail
        );
    }

    /**
     * Create reservation notification (untuk PENGAJU saat Zoom dibuat).
     * WA TIDAK dikirim ke pengaju di sini -- WA Zoom baru hanya ke admin.
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
            false,
            $sendEmail
        );
    }

    /**
     * Create reservation approved notification.
     * Disetujui di luar 4 kondisi WA (WA khusus saat link Zoom siap) -> WA tidak dikirim.
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
            false,
            $sendEmail
        );
    }

    /**
     * Create asset notification.
     * Di luar 4 kondisi WA -> WA tidak dikirim.
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
            false,
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
