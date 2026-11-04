<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Kirim reminder WA Fonnte ke teknisi pada hari Zoom dimulai.
// Pastikan cron server menjalankan `php artisan schedule:run` setiap menit.
Schedule::command('zoom:send-start-reminders')->dailyAt('07:00')->withoutOverlapping();
