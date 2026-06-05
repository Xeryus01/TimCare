<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php
    $user = auth()->user();
    $isAdminOrTechnician = $user->hasRole(['Admin', 'Teknisi']);

    if ($isAdminOrTechnician) {
        // Admin/Teknisi melihat semua data
        $totalTickets = \App\Models\Ticket::count();
        $totalZooms = \App\Models\Reservation::count();
        $layananSelesai = \App\Models\Ticket::whereIn('status', [\App\Models\Ticket::STATUS_SOLVED, \App\Models\Ticket::STATUS_SOLVED_WITH_NOTES])->count()
            + \App\Models\Reservation::where('status', \App\Models\Reservation::STATUS_COMPLETED)->count();
        $totalLayanan = $totalTickets + $totalZooms;
        $capaianPersentase = $totalLayanan > 0 ? round(($layananSelesai / $totalLayanan) * 100, 1) : 0;

        $ticketCounts = [
            'Dibuka' => \App\Models\Ticket::where('status', \App\Models\Ticket::STATUS_OPEN)->count(),
            'Diproses Teknisi' => \App\Models\Ticket::where('status', \App\Models\Ticket::STATUS_ASSIGNED_DETECT)->count(),
            'Menunggu Ketersediaan Barang' => \App\Models\Ticket::where('status', \App\Models\Ticket::STATUS_WAITING_PARTS)->count(),
            'Selesai + Catatan' => \App\Models\Ticket::where('status', \App\Models\Ticket::STATUS_SOLVED_WITH_NOTES)->count(),
            'Selesai' => \App\Models\Ticket::where('status', \App\Models\Ticket::STATUS_SOLVED)->count(),
            'Batal' => \App\Models\Ticket::whereIn('status', [\App\Models\Ticket::STATUS_REJECTED, \App\Models\Ticket::STATUS_CANCELLED])->count(),
        ];

        $zoomCounts = [
            'Dibuka' => \App\Models\Reservation::where('status', \App\Models\Reservation::STATUS_PENDING)->count(),
            'Diproses Teknisi' => \App\Models\Reservation::where('status', \App\Models\Reservation::STATUS_APPROVED)->count(),
            'Zoom Siap' => \App\Models\Reservation::where('status', \App\Models\Reservation::STATUS_WAITING_MONITORING)->count(),
            'Selesai' => \App\Models\Reservation::where('status', \App\Models\Reservation::STATUS_COMPLETED)->count(),
            'Selesai Ditolak' => \App\Models\Reservation::where('status', \App\Models\Reservation::STATUS_REJECTED)->count(),
            'Batal' => \App\Models\Reservation::where('status', \App\Models\Reservation::STATUS_CANCELLED)->count(),
        ];

        $layananBelumDilayani = \App\Models\Ticket::whereNotIn('status', [\App\Models\Ticket::STATUS_SOLVED, \App\Models\Ticket::STATUS_SOLVED_WITH_NOTES])->count();
        $zoomBelumDilayani = \App\Models\Reservation::where('status', '!=', \App\Models\Reservation::STATUS_COMPLETED)->count();

        // Ambil data terbaru untuk ditampilkan
        $recentTickets = \App\Models\Ticket::latest()->take(5)->get();
        $recentReservations = \App\Models\Reservation::latest()->take(5)->get();

        // Data untuk kalender zoom - semua reservasi dengan Zoom link dalam bulan ini
        $zoomEvents = \App\Models\Reservation::whereNotNull('zoom_link')
            ->where('zoom_link', '!=', '')
            ->where('start_time', '>=', now()->startOfMonth())
            ->where('end_time', '<=', now()->endOfMonth())
            ->get(['start_time', 'end_time', 'room_name', 'purpose', 'status', 'code', 'participants_count', 'operator_needed', 'breakroom_needed', 'id']);

        // Data untuk kalender piket - semua jadwal piket dalam bulan ini
        $piketEvents = \App\Models\PiketSchedule::where('week_start_date', '>=', now()->startOfMonth()->toDateString())
            ->where('week_start_date', '<=', now()->endOfMonth()->toDateString())
            ->get(['id', 'week_start_date', 'technician_1', 'technician_2', 'technician_3']);

        $activePiketSchedules = $piketEvents->filter(function ($schedule) {
            $weekStart = \Illuminate\Support\Carbon::parse($schedule->week_start_date);
            $weekEnd = $weekStart->copy()->addDays(6);
            return now()->between($weekStart, $weekEnd);
        })->map(function ($schedule) {
            return [
                'week_start_date' => \Illuminate\Support\Carbon::parse($schedule->week_start_date)->format('d M Y'),
                'week_end_date' => \Illuminate\Support\Carbon::parse($schedule->week_start_date)->addDays(6)->format('d M Y'),
                'technicians' => array_filter([$schedule->technician_1, $schedule->technician_2, $schedule->technician_3]),
            ];
        })->values();

        $zoomEventsArray = $zoomEvents->map(function($event) {
            $statusColors = [
                'PENDING' => '#fbbf24', // yellow
                'APPROVED' => '#3b82f6', // blue
                'WAITING_MONITORING' => '#f59e0b', // amber
                'COMPLETED' => '#10b981', // green
                'REJECTED' => '#ef4444', // red
                'CANCELLED' => '#6b7280', // gray
            ];

            $statusLabels = [
                'PENDING' => 'Dibuka',
                'APPROVED' => 'Diproses Teknisi',
                'WAITING_MONITORING' => 'Zoom Siap',
                'COMPLETED' => 'Selesai',
                'REJECTED' => 'Ditolak',
                'CANCELLED' => 'Batal',
            ];

            return [
                'title' => $event->room_name . ' - ' . $event->code,
                'start' => $event->start_time->toISOString(),
                'end' => $event->end_time->toISOString(),
                'url' => route('reservations.edit', $event),
                'display' => 'block',
                'backgroundColor' => $statusColors[$event->status] ?? '#6b7280',
                'borderColor' => $statusColors[$event->status] ?? '#6b7280',
                'extendedProps' => [
                    'code' => $event->code,
                    'purpose' => $event->purpose,
                    'participants' => $event->participants_count,
                    'operator' => $event->operator_needed ? 'Ya' : 'Tidak',
                    'breakroom' => $event->breakroom_needed ? 'Ya' : 'Tidak',
                    'status' => $statusLabels[$event->status] ?? 'Unknown',
                    'room' => $event->room_name,
                    'start' => $event->start_time->format('d/m/Y H:i'),
                    'end' => $event->end_time->format('d/m/Y H:i'),
                ]
            ];
        })->toArray();
    } else {
        // User biasa melihat semua data di dashboard, tapi hanya yang mereka ajukan di daftar
        $totalTickets = \App\Models\Ticket::count();
        $totalZooms = \App\Models\Reservation::count();
        $layananSelesai = \App\Models\Ticket::whereIn('status', [\App\Models\Ticket::STATUS_SOLVED, \App\Models\Ticket::STATUS_SOLVED_WITH_NOTES])->count()
            + \App\Models\Reservation::where('status', \App\Models\Reservation::STATUS_COMPLETED)->count();
        $totalLayanan = $totalTickets + $totalZooms;
        $capaianPersentase = $totalLayanan > 0 ? round(($layananSelesai / $totalLayanan) * 100, 1) : 0;

        $ticketCounts = [
            'Dibuka' => \App\Models\Ticket::where('status', \App\Models\Ticket::STATUS_OPEN)->count(),
            'Diproses Teknisi' => \App\Models\Ticket::where('status', \App\Models\Ticket::STATUS_ASSIGNED_DETECT)->count(),
            'Menunggu Ketersediaan Barang' => \App\Models\Ticket::where('status', \App\Models\Ticket::STATUS_WAITING_PARTS)->count(),
            'Selesai + Catatan' => \App\Models\Ticket::where('status', \App\Models\Ticket::STATUS_SOLVED_WITH_NOTES)->count(),
            'Selesai' => \App\Models\Ticket::where('status', \App\Models\Ticket::STATUS_SOLVED)->count(),
            'Batal' => \App\Models\Ticket::whereIn('status', [\App\Models\Ticket::STATUS_REJECTED, \App\Models\Ticket::STATUS_CANCELLED])->count(),
        ];

        $zoomCounts = [
            'Dibuka' => \App\Models\Reservation::where('status', \App\Models\Reservation::STATUS_PENDING)->count(),
            'Diproses Teknisi' => \App\Models\Reservation::where('status', \App\Models\Reservation::STATUS_APPROVED)->count(),
            'Zoom Siap' => \App\Models\Reservation::where('status', \App\Models\Reservation::STATUS_WAITING_MONITORING)->count(),
            'Selesai' => \App\Models\Reservation::where('status', \App\Models\Reservation::STATUS_COMPLETED)->count(),
            'Selesai Ditolak' => \App\Models\Reservation::where('status', \App\Models\Reservation::STATUS_REJECTED)->count(),
            'Batal' => \App\Models\Reservation::where('status', \App\Models\Reservation::STATUS_CANCELLED)->count(),
        ];

        $layananBelumDilayani = \App\Models\Ticket::whereNotIn('status', [\App\Models\Ticket::STATUS_SOLVED, \App\Models\Ticket::STATUS_SOLVED_WITH_NOTES])->count();
        $zoomBelumDilayani = \App\Models\Reservation::where('status', '!=', \App\Models\Reservation::STATUS_COMPLETED)->count();

        // Ambil data terbaru milik user
        $recentTickets = \App\Models\Ticket::where('requester_id', $user->id)->latest()->take(5)->get();
        $recentReservations = \App\Models\Reservation::where('requester_id', $user->id)->latest()->take(5)->get();

        // Data untuk kalender zoom - semua reservasi dengan Zoom link dalam bulan ini
        $zoomEvents = \App\Models\Reservation::whereNotNull('zoom_link')
            ->where('zoom_link', '!=', '')
            ->where('start_time', '>=', now()->startOfMonth())
            ->where('end_time', '<=', now()->endOfMonth())
            ->get(['start_time', 'end_time', 'room_name', 'purpose', 'status', 'code', 'participants_count', 'operator_needed', 'breakroom_needed', 'id']);

        $piketEvents = \App\Models\PiketSchedule::where('week_start_date', '>=', now()->startOfMonth()->toDateString())
            ->where('week_start_date', '<=', now()->endOfMonth()->toDateString())
            ->get(['id', 'week_start_date', 'technician_1', 'technician_2', 'technician_3']);

        $activePiketSchedules = $piketEvents->filter(function ($schedule) {
            $weekStart = \Illuminate\Support\Carbon::parse($schedule->week_start_date);
            $weekEnd = $weekStart->copy()->addDays(6);
            return now()->between($weekStart, $weekEnd);
        })->map(function ($schedule) {
            return [
                'week_start_date' => \Illuminate\Support\Carbon::parse($schedule->week_start_date)->format('d M Y'),
                'week_end_date' => \Illuminate\Support\Carbon::parse($schedule->week_start_date)->addDays(6)->format('d M Y'),
                'technicians' => array_filter([$schedule->technician_1, $schedule->technician_2, $schedule->technician_3]),
            ];
        })->values();

        $zoomEventsArray = $zoomEvents->map(function($event) {
            $statusColors = [
                'PENDING' => '#fbbf24', // yellow
                'APPROVED' => '#3b82f6', // blue
                'WAITING_MONITORING' => '#f59e0b', // amber
                'COMPLETED' => '#10b981', // green
                'REJECTED' => '#ef4444', // red
                'CANCELLED' => '#6b7280', // gray
            ];

            $statusLabels = [
                'PENDING' => 'Dibuka',
                'APPROVED' => 'Diproses Teknisi',
                'WAITING_MONITORING' => 'Zoom Siap',
                'COMPLETED' => 'Selesai',
                'REJECTED' => 'Ditolak',
                'CANCELLED' => 'Batal',
            ];

            return [
                'title' => $event->room_name . ' - ' . $event->code,
                'start' => $event->start_time->toISOString(),
                'end' => $event->end_time->toISOString(),
                'url' => route('reservations.edit', $event),
                'display' => 'block',
                'backgroundColor' => $statusColors[$event->status] ?? '#6b7280',
                'borderColor' => $statusColors[$event->status] ?? '#6b7280',
                'extendedProps' => [
                    'code' => $event->code,
                    'purpose' => $event->purpose,
                    'participants' => $event->participants_count,
                    'operator' => $event->operator_needed ? 'Ya' : 'Tidak',
                    'breakroom' => $event->breakroom_needed ? 'Ya' : 'Tidak',
                    'status' => $statusLabels[$event->status] ?? 'Unknown',
                    'room' => $event->room_name,
                    'start' => $event->start_time->format('d/m/Y H:i'),
                    'end' => $event->end_time->format('d/m/Y H:i'),
                ]
            ];
        })->toArray();
    }
?>

<div class="min-h-screen">
    <div class="p-4 sm:p-5 lg:p-7.5 xl:p-9">
        <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white truncate">Ringkasan Layanan</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500 dark:text-gray-400 truncate">Halo, <?php echo e(auth()->user()->name); ?>. Pantau tiket, pengajuan Zoom, dan performa.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(url()->to(route('tickets.create'))); ?>" class="rounded-lg bg-brand-600 px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium text-white hover:bg-brand-700 transition-colors whitespace-nowrap">Ajukan Tiket</a>
                <a href="<?php echo e(url()->to(route('reservations.create'))); ?>" class="rounded-lg border border-brand-600 px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium text-brand-600 hover:bg-brand-50 dark:hover:bg-brand-500/10 transition-colors whitespace-nowrap">Ajukan Zoom</a>
            </div>
        </div>

        <div class="mb-6 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-3 sm:p-5 dark:border-gray-700 dark:bg-dark-800">
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 truncate">Total Tiket Masalah</p>
                <h3 class="mt-2 text-2xl sm:text-3xl font-bold text-red-600 dark:text-red-400"><?php echo e($totalTickets); ?></h3>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-3 sm:p-5 dark:border-gray-700 dark:bg-dark-800">
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 truncate">Total Tiket Zoom</p>
                <h3 class="mt-2 text-2xl sm:text-3xl font-bold text-blue-600 dark:text-blue-400"><?php echo e($totalZooms); ?></h3>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-3 sm:p-5 dark:border-gray-700 dark:bg-dark-800">
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 truncate">Layanan Selesai</p>
                <h3 class="mt-2 text-2xl sm:text-3xl font-bold text-green-600 dark:text-green-400"><?php echo e($layananSelesai); ?></h3>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-3 sm:p-5 dark:border-gray-700 dark:bg-dark-800">
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 truncate">Capaian (%)</p>
                <h3 class="mt-2 text-2xl sm:text-3xl font-bold text-purple-600 dark:text-purple-400"><?php echo e($capaianPersentase); ?>%</h3>
            </div>
        </div>

        <div class="mb-6 rounded-xl border border-gray-200 bg-white p-4 sm:p-5 dark:border-gray-700 dark:bg-dark-800">
            <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">Tim Piket Aktif</h2>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Tampilkan jadwal tim piket minggu ini.</p>
                </div>
            </div>
            <?php if($activePiketSchedules->isNotEmpty()): ?>
                <div class="space-y-3">
                    <?php $__currentLoopData = $activePiketSchedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-dark-800">
                            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">Minggu <?php echo e($schedule['week_start_date']); ?> - <?php echo e($schedule['week_end_date']); ?></p>
                                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">Aktif</span>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-2 text-sm">
                                <?php $__currentLoopData = $schedule['technicians']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $technician): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="inline-flex items-center rounded-2xl border border-gray-200 bg-gray-50 px-4 py-2 text-sm font-semibold text-gray-900 dark:border-gray-700 dark:bg-white/10 dark:text-white"><?php echo e($technician); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada jadwal piket aktif untuk minggu ini.</p>
            <?php endif; ?>
        </div>

        <!-- Kalender Zoom -->
        <div class="mb-6 rounded-xl border border-gray-200 bg-white p-4 sm:p-5 dark:border-gray-700 dark:bg-dark-800">
            <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">Kalender Zoom</h2>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Hanya Zoom dengan link yang muncul. Semua event akan tampil vertikal dalam satu hari.</p>
                </div>
                <div class="flex flex-wrap gap-2 text-xs">
                    <span class="flex items-center gap-1"><div class="w-3 h-3 bg-blue-500 rounded"></div> Approved</span>
                </div>
            </div>
            <div class="h-80">
                <div id="zoomCalendar" class="w-full h-full"></div>
            </div>
        </div>

        <!-- Layanan Belum Dilayani
        <div class="mb-6 grid grid-cols-1 gap-3 sm:gap-4 md:grid-cols-3">
            <div class="rounded-xl border border-gray-200 bg-white p-3 sm:p-5 dark:border-gray-700 dark:bg-dark-800">
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 truncate">Layanan Belum Dilayani</p>
                <h3 class="mt-2 text-2xl sm:text-3xl font-bold text-orange-600 dark:text-orange-400"><?php echo e($layananBelumDilayani); ?></h3>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-3 sm:p-5 dark:border-gray-700 dark:bg-dark-800">
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 truncate">Zoom Belum Dilayani</p>
                <h3 class="mt-2 text-2xl sm:text-3xl font-bold text-cyan-600 dark:text-cyan-400"><?php echo e($zoomBelumDilayani); ?></h3>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-3 sm:p-5 dark:border-gray-700 dark:bg-dark-800">
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 truncate">Total Semua Layanan</p>
                <h3 class="mt-2 text-2xl sm:text-3xl font-bold text-indigo-600 dark:text-indigo-400"><?php echo e($totalLayanan); ?></h3>
            </div>
        </div> -->

        <div class="mb-6 grid gap-4 sm:gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 dark:border-gray-700 dark:bg-dark-800 overflow-hidden">
                <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <h2 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white truncate">Grafik Tiket Permasalahan</h2>
                    <div class="flex items-center gap-2">
                        <select id="monthFilter" class="text-xs sm:text-sm border border-gray-300 rounded px-2 py-1 dark:border-gray-600 dark:bg-dark-700 dark:text-white">
                            <option value="all">Semua</option>
                            <?php for($i = 1; $i <= 12; $i++): ?>
                                <option value="<?php echo e(date('Y')); ?>-<?php echo e(str_pad($i, 2, '0', STR_PAD_LEFT)); ?>" <?php echo e($i == date('n') ? 'selected' : ''); ?>>
                                    <?php echo e(\Carbon\Carbon::create(date('Y'), $i, 1)->locale('id')->format('F Y')); ?>

                                </option>
                            <?php endfor; ?>
                        </select>
                        <a href="<?php echo e(url()->to(route('exports.tickets'))); ?>" class="text-xs sm:text-sm font-medium text-brand-600 hover:text-brand-700 whitespace-nowrap">Ekspor CSV</a>
                    </div>
                </div>
                <div class="relative h-72">
                    <canvas id="ticketChart" class="w-full h-full"></canvas>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 dark:border-gray-700 dark:bg-dark-800 overflow-hidden">
                <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <h2 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white truncate">Grafik Pengajuan Zoom</h2>
                    <a href="<?php echo e(url()->to(route('exports.reservations'))); ?>" class="text-xs sm:text-sm font-medium text-brand-600 hover:text-brand-700 whitespace-nowrap">Ekspor CSV</a>
                </div>
                <div class="relative h-72">
                    <canvas id="zoomChart" class="w-full h-full"></canvas>
                </div>
            </div>
        </div>

        <div class="grid gap-4 sm:gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-dark-800 overflow-hidden">
                <div class="border-b border-gray-200 px-4 sm:px-5 py-3 sm:py-4 dark:border-gray-700">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white truncate">Tiket Terbaru</h3>
                </div>
                <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto dark:divide-gray-700">
                    <?php $__empty_1 = true; $__currentLoopData = $recentTickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-4 sm:px-5 py-3 sm:py-4">
                            <div class="min-w-0 flex-1">
                                <p class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white truncate"><?php echo e($ticket->title); ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate"><?php echo e($ticket->code); ?> • <?php echo e($ticket->category_label); ?></p>
                            </div>
                            <span class="rounded-full bg-gray-100 px-2 sm:px-2.5 py-1 text-xs font-medium text-gray-700 dark:bg-white/5 dark:text-gray-300 whitespace-nowrap"><?php echo e($ticket->status_label); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="px-4 sm:px-5 py-6 sm:py-8 text-center text-xs sm:text-sm text-gray-500 dark:text-gray-400">Belum ada tiket.</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-dark-800 overflow-hidden">
                <div class="border-b border-gray-200 px-4 sm:px-5 py-3 sm:py-4 dark:border-gray-700">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white truncate">Pengajuan Zoom Terbaru</h3>
                </div>
                <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto dark:divide-gray-700">
                    <?php $__empty_1 = true; $__currentLoopData = $recentReservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reservation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-4 sm:px-5 py-3 sm:py-4">
                            <div class="min-w-0 flex-1">
                                <p class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white truncate"><?php echo e($reservation->room_name); ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate"><?php echo e(optional($reservation->start_time)->format('d/m/Y H:i')); ?> • <?php echo e(\Illuminate\Support\Str::limit($reservation->purpose, 40)); ?></p>
                            </div>
                            <span class="rounded-full bg-gray-100 px-2 sm:px-2.5 py-1 text-xs font-medium text-gray-700 dark:bg-white/5 dark:text-gray-300 whitespace-nowrap"><?php echo e($reservation->status_label); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="px-4 sm:px-5 py-6 sm:py-8 text-center text-xs sm:text-sm text-gray-500 dark:text-gray-400">Belum ada pengajuan Zoom.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<style>
    #zoomCalendar .fc .fc-daygrid-event,
    #zoomCalendar .fc .fc-timegrid-event {
        padding: 0.2rem 0.35rem;
        font-size: 0.72rem;
        border-radius: 0.45rem;
        line-height: 1.1;
    }
    #zoomCalendar .fc .fc-timegrid-event {
        margin-bottom: 0.15rem;
    }
    #zoomCalendar .fc .fc-event-main-frame {
        padding: 0.15rem 0.35rem;
    }
    #zoomCalendar .fc .fc-toolbar-title {
        font-size: 0.95rem;
    }
    #zoomCalendar .fc .fc-toolbar .fc-button {
        font-size: 0.75rem;
        padding: 0.35rem 0.6rem;
    }
    #zoomCalendar .fc .fc-daygrid-day-top {
        padding: 0.35rem 0.45rem;
    }
    #zoomCalendar .fc .fc-daygrid-more-link {
        font-size: 0.7rem;
    }

    /* Batasi tinggi kalender dan tambahkan overflow untuk mencegah layout meluber */
    #zoomCalendar {
        max-height: 20rem; /* 320px */
        overflow: auto;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Inisialisasi Kalender Zoom
        const calendarEl = document.getElementById('zoomCalendar');
        if (calendarEl) {
            let tooltipEl = null;

            const removeTooltip = function() {
                if (tooltipEl) {
                    document.body.removeChild(tooltipEl);
                    tooltipEl = null;
                }
            };

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: <?php echo json_encode($zoomEventsArray, 15, 512) ?>,
                height: 320,
                contentHeight: 320,
                eventDisplay: 'block',
                views: {
                    dayGridMonth: {
                        dayMaxEvents: 3,
                        dayMaxEventRows: 3
                    },
                    timeGridWeek: {
                        dayMaxEvents: 3,
                        slotEventOverlap: false
                    },
                    timeGridDay: {
                        dayMaxEvents: 3,
                        slotEventOverlap: false
                    }
                },
                moreLinkContent: function(arg) {
                    return `+${arg.num} lainnya`;
                },
                eventOrder: 'start,-duration',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: false
                },
                eventOverlap: false,
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    if (info.event.url) {
                        window.location.href = info.event.url;
                    }
                },
                eventMouseEnter: function(info) {
                    removeTooltip();
                    const props = info.event.extendedProps;
                    const tooltip = `
                        <div class="bg-gray-900 text-white p-3 rounded-lg shadow-lg max-w-[18rem]">
                            <div class="font-semibold mb-2">${props.room} • ${props.code}</div>
                            <div class="text-xs sm:text-sm space-y-1">
                                <div><strong>Tujuan:</strong> ${props.purpose}</div>
                                <div><strong>Status:</strong> ${props.status}</div>
                                <div><strong>Peserta:</strong> ${props.participants}</div>
                                <div><strong>Operator:</strong> ${props.operator}</div>
                                <div><strong>Breakroom:</strong> ${props.breakroom}</div>
                                <div><strong>Mulai:</strong> ${props.start}</div>
                                <div><strong>Selesai:</strong> ${props.end}</div>
                            </div>
                        </div>
                    `;

                    tooltipEl = document.createElement('div');
                    tooltipEl.innerHTML = tooltip;
                    tooltipEl.style.position = 'absolute';
                    tooltipEl.style.zIndex = '9999';
                    tooltipEl.style.pointerEvents = 'none';
                    document.body.appendChild(tooltipEl);

                    const rect = info.el.getBoundingClientRect();
                    tooltipEl.style.left = `${rect.left}px`;
                    tooltipEl.style.top = `${rect.top - tooltipEl.offsetHeight - 10}px`;
                },
                eventMouseLeave: function() {
                    removeTooltip();
                }
            });
            calendar.render();
        }

        let ticketChart, zoomChart;

        const initCharts = function(ticketData, zoomData) {
            const ticketCtx = document.getElementById('ticketChart');
            const zoomCtx = document.getElementById('zoomChart');

            if (ticketCtx) {
                if (ticketChart) ticketChart.destroy();
                ticketChart = new Chart(ticketCtx, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(ticketData),
                        datasets: [{
                            label: 'Jumlah tiket',
                            data: Object.values(ticketData),
                            backgroundColor: ['#ef4444', '#f59e0b', '#6366f1', '#10b981', '#3b82f6', '#6b7280'],
                            borderRadius: 8,
                            barPercentage: 0.65,
                            categoryPercentage: 0.7,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { grid: { display: false } },
                            y: { beginAtZero: true, ticks: { precision: 0, stepSize: 1 } }
                        }
                    }
                });
            }

            if (zoomCtx) {
                if (zoomChart) zoomChart.destroy();
                zoomChart = new Chart(zoomCtx, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(zoomData),
                        datasets: [{
                            data: Object.values(zoomData),
                            backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#6b7280', '#8b5cf6'],
                            hoverOffset: 8,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 12, padding: 16 } }
                        }
                    }
                });
            }
        };

        // Initial load
        initCharts(<?php echo json_encode($ticketCounts, 15, 512) ?>, <?php echo json_encode($zoomCounts, 15, 512) ?>);

        // Month filter
        const monthFilter = document.getElementById('monthFilter');
        if (monthFilter) {
            monthFilter.addEventListener('change', function() {
                const month = this.value;
                const chartUrl = '<?php echo e(url('/api/dashboard/charts')); ?>';
                fetch(`${chartUrl}?month=${encodeURIComponent(month)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    initCharts(data.ticketCounts, data.zoomCounts);
                })
                .catch(error => console.error('Error fetching chart data:', error));
            });
        }
    });
</script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\Users\BPS 1900\Documents\timcare\resources\views/dashboard.blade.php ENDPATH**/ ?>