<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotificationDelivery implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted before failing.
     */
    public int $tries = 3;

    /**
     * Seconds to wait before retrying a failed attempt.
     */
    public int $backoff = 30;

    public function __construct(
        public int $notificationId,
        public bool $sendWhatsApp = true,
        public bool $sendEmail = true,
    ) {
    }

    /**
     * Deliver the WhatsApp message and/or email for an already-created notification.
     *
     * Dipanggil via dispatchAfterResponse(): berjalan SETELAH respons HTTP terkirim,
     * dalam proses request yang sama, TANPA perlu `php artisan queue:work`.
     */
    public function handle(NotificationService $service): void
    {
        $notification = Notification::find($this->notificationId);
        if (! $notification) {
            return;
        }

        $user = User::find($notification->user_id);
        if (! $user) {
            return;
        }

        if ($this->sendWhatsApp && ! empty($user->phone_number)) {
            $service->sendWhatsAppNotification($user, $notification);
        }

        if ($this->sendEmail && ! empty($user->email)) {
            $service->sendEmailNotification($user, $notification);
        }
    }
}
