<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\Reservation;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendZoomStartReminder extends Command
{
    protected $signature = 'zoom:send-start-reminders {--date= : Tanggal target format Y-m-d. Default hari ini} {--dry-run : Tampilkan daftar tanpa mengirim notifikasi}';

    protected $description = 'Kirim reminder WhatsApp Fonnte ke teknisi pada hari Zoom harus dimulai.';

    public function handle(NotificationService $notificationService): int
    {
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'), config('app.timezone'))->startOfDay()
            : now(config('app.timezone'))->startOfDay();

        $start = $date->copy()->startOfDay();
        $end = $date->copy()->endOfDay();

        $reservations = Reservation::with(['requester', 'approver'])
            ->whereBetween('start_time', [$start, $end])
            ->whereIn('status', [Reservation::STATUS_APPROVED, Reservation::STATUS_WAITING_MONITORING])
            ->whereNotNull('approver_id')
            ->orderBy('start_time')
            ->get();

        if ($reservations->isEmpty()) {
            $this->info('Tidak ada Zoom yang perlu diingatkan untuk '.$date->format('d/m/Y').'.');
            return self::SUCCESS;
        }

        $sent = 0;
        foreach ($reservations as $reservation) {
            $technician = $reservation->approver;

            if (! $technician instanceof User) {
                continue;
            }

            $alreadyExists = Notification::where('user_id', $technician->id)
                ->where('action_type', 'reservation')
                ->where('action_id', $reservation->id)
                ->where('title', '⏰ Reminder Zoom Hari Ini')
                ->whereDate('created_at', $date->toDateString())
                ->exists();

            if ($alreadyExists) {
                $this->line("Lewati {$reservation->code}: reminder hari ini sudah pernah dibuat untuk {$technician->name}.");
                continue;
            }

            if ($this->option('dry-run')) {
                $this->line("[DRY RUN] {$reservation->code} -> {$technician->name} ({$technician->phone_number})");
                continue;
            }

            $notificationService->notifyReservationStartReminder($technician, $reservation);
            $sent++;

            $this->info("Reminder dikirim untuk {$reservation->code} ke {$technician->name}.");
        }

        $this->info("Selesai. {$sent} reminder dibuat/dikirim.");

        return self::SUCCESS;
    }
}
