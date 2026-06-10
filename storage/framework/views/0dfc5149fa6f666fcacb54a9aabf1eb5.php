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
<div class="min-h-screen">
    <div class="p-5 sm:p-7.5 lg:p-9">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Jadwal Piket Mingguan</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Lihat jadwal piket tim IT untuk setiap minggu</p>
        </div>

        <?php if(count($schedules) === 0): ?>
            <div class="mb-4 rounded-lg border border-yellow-400 bg-yellow-100 p-4 text-yellow-700 dark:border-yellow-500/30 dark:bg-yellow-500/15 dark:text-yellow-400">
                <p class="font-semibold">Belum ada jadwal piket.</p>
                <p class="text-sm mt-1">Jadwal piket belum dibuat oleh admin.</p>
            </div>
        <?php endif; ?>

        <?php
            $currentSchedule = collect($schedules)->first(function ($schedule) {
                $start = \Carbon\Carbon::parse($schedule->week_start_date);
                $end = $schedule->week_end_date
                    ? \Carbon\Carbon::parse($schedule->week_end_date)
                    : $start->copy()->addDays(6);
                return now()->between($start->startOfDay(), $end->endOfDay());
            });

            $nextSchedule = collect($schedules)
                ->filter(function ($schedule) {
                    return \Carbon\Carbon::parse($schedule->week_start_date)->isAfter(now());
                })
                ->sortBy('week_start_date')
                ->first();

            $calendarEvents = collect($schedules)->map(function ($schedule) {
                $start = \Carbon\Carbon::parse($schedule->week_start_date);
                $end = $schedule->week_end_date
                    ? \Carbon\Carbon::parse($schedule->week_end_date)
                    : $start->copy()->addDays(6);
                return [
                    'title' => "Piket: {$start->format('d/m')} - {$end->format('d/m')}",
                    'start' => $start->toDateString(),
                    'end' => $end->copy()->addDay()->toDateString(),
                    'allDay' => true,
                    'backgroundColor' => '#8b5cf6',
                    'borderColor' => '#8b5cf6',
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'id' => $schedule->id,
                        'technician_1' => $schedule->technician_1,
                        'technician_2' => $schedule->technician_2,
                        'technician_3' => $schedule->technician_3,
                        'start_date' => $start->format('d/m/Y'),
                        'end_date' => $end->format('d/m/Y'),
                    ],
                ];
            })->toArray();
        ?>

        <div class="grid gap-4 xl:grid-cols-[0.95fr_1.05fr] mb-6">
            <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-dark-800">
                <p class="text-sm uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400">Ringkasan Piket</p>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-3xl bg-gray-50 p-4 dark:bg-gray-900">
                        <p class="text-xs uppercase tracking-[0.25em] text-gray-500 dark:text-gray-400">Minggu Aktif</p>
                        <?php if($currentSchedule): ?>
                            <?php
                                $activeStart = \Carbon\Carbon::parse($currentSchedule->week_start_date);
                                $activeEnd = $currentSchedule->week_end_date
                                    ? \Carbon\Carbon::parse($currentSchedule->week_end_date)
                                    : $activeStart->copy()->addDays(6);
                            ?>
                            <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white"><?php echo e($activeStart->format('d M')); ?> — <?php echo e($activeEnd->format('d M Y')); ?></p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-200">Petugas: <?php echo e($currentSchedule->technician_1); ?>, <?php echo e($currentSchedule->technician_2); ?>, <?php echo e($currentSchedule->technician_3); ?></p>
                        <?php else: ?>
                            <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">Tidak ada jadwal</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Silakan hubungi admin untuk jadwal piket.</p>
                        <?php endif; ?>
                    </div>
                    <div class="rounded-3xl bg-gray-50 p-4 dark:bg-gray-900">
                        <p class="text-xs uppercase tracking-[0.25em] text-gray-500 dark:text-gray-400">Jadwal Selanjutnya</p>
                        <?php if($nextSchedule): ?>
                            <?php
                                $nextStart = \Carbon\Carbon::parse($nextSchedule->week_start_date);
                                $nextEnd = $nextSchedule->week_end_date
                                    ? \Carbon\Carbon::parse($nextSchedule->week_end_date)
                                    : $nextStart->copy()->addDays(6);
                            ?>
                            <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white"><?php echo e($nextStart->format('d M')); ?> — <?php echo e($nextEnd->format('d M Y')); ?></p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-200">Petugas: <?php echo e($nextSchedule->technician_1); ?>, <?php echo e($nextSchedule->technician_2); ?></p>
                        <?php else: ?>
                            <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">Tidak ada jadwal berikutnya</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Semua jadwal sudah terisi.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-dark-800">
                <p class="text-sm uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400">Rincian Tugas Piket</p>
                <h2 class="mt-2 text-xl font-semibold text-gray-900 dark:text-white">Tugas Teknisi Saat Piket</h2>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">
                        <p class="font-semibold text-gray-900 dark:text-white">Penyiapan Video Conference</p>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Siapkan perangkat, koneksi, dan pengaturan audio/video untuk pertemuan online.</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">
                        <p class="font-semibold text-gray-900 dark:text-white">Troubleshooting Jaringan & Keamanan</p>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Tangani gangguan jaringan, konfigurasi keamanan, dan akses sistem.</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">
                        <p class="font-semibold text-gray-900 dark:text-white">Troubleshooting Aset TIK</p>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Perbaiki masalah hardware atau software pada perangkat TIK.</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">
                        <p class="font-semibold text-gray-900 dark:text-white">Operator Rilis BRS</p>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Bantu proses rilis BRS bulanan dan pastikan operasional berjalan lancar.</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">
                        <p class="font-semibold text-gray-900 dark:text-white">Pemasangan Infrastruktur</p>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Pasang dan verifikasi jaringan, perangkat, serta aspek keamanan TI.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-dark-800">
            <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400">Kalender Piket</p>
                    <h2 class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">Jadwal Mingguan</h2>
                </div>
                <span class="inline-flex items-center rounded-full bg-brand-50 px-3 py-1 text-sm font-semibold text-brand-700"><?php echo e(count($schedules)); ?> jadwal</span>
            </div>

            <div class="min-h-[520px] w-full overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm dark:border-gray-800 dark:bg-dark-900" id="piketCalendar"></div>
        </div>

        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
        <style>
            #piketCalendar { min-height: 520px; }
            #piketCalendar .fc { min-height: 100%; }
            #piketCalendar .fc .fc-toolbar-title { font-size: 1rem; font-weight: 600; }
            #piketCalendar .fc .fc-daygrid-day-top { padding: 0.55rem 0.65rem; }
            #piketCalendar .fc .fc-daygrid-event { padding: 0.4rem 0.45rem; font-size: 0.75rem; border-radius: 0.55rem; }
            #piketCalendar .fc .fc-daygrid-more-link { font-size: 0.75rem; }
            #piketCalendar .fc .fc-event-title { white-space: normal; line-height: 1.15; }
            #piketCalendar .fc .fc-event-crew { color: rgba(255,255,255,0.85); font-size: 0.65rem; line-height: 1.15; }
        </style>
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const calendarEl = document.getElementById('piketCalendar');
                if (!calendarEl) return;

                let tooltipEl = null;

                const removeTooltip = function() {
                    if (tooltipEl) {
                        document.body.removeChild(tooltipEl);
                        tooltipEl = null;
                    }
                };

                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    height: 'auto',
                    contentHeight: 'auto',
                    aspectRatio: 1.35,
                    events: <?php echo json_encode($calendarEvents, 15, 512) ?>,
                    eventDisplay: 'block',
                    dayMaxEventRows: 2,
                    dayMaxEvents: true,
                    eventTimeFormat: { hour: '2-digit', minute: '2-digit', meridiem: false },
                    eventContent: function(arg) {
                        const props = arg.event.extendedProps;
                        const names = [props.technician_1, props.technician_2, props.technician_3].filter(Boolean).join(', ');
                        const title = arg.event.title;

                        return {
                            html: `
                                <div class="fc-event-title">${title}</div>
                                <div class="fc-event-crew text-[0.68rem] mt-1">${names}</div>
                            `
                        };
                    },
                    eventDidMount: function(info) {
                        info.el.style.padding = '0.45rem';
                        info.el.style.borderRadius = '0.85rem';
                        info.el.style.cursor = 'default';
                    },
                    eventMouseEnter: function(info) {
                        removeTooltip();
                        const props = info.event.extendedProps;
                        const tooltip = `
                            <div class="bg-gray-900 text-white p-3 rounded-lg shadow-lg max-w-[18rem]">
                                <div class="font-semibold mb-2">Jadwal Piket Mingguan</div>
                                <div class="text-xs sm:text-sm space-y-1">
                                    <div><strong>Mulai:</strong> ${props.start_date}</div>
                                    <div><strong>Selesai:</strong> ${props.end_date}</div>
                                    <div class="border-t border-gray-700 mt-2 pt-2">
                                        <div><strong>Petugas 1:</strong> ${props.technician_1}</div>
                                        <div><strong>Petugas 2:</strong> ${props.technician_2}</div>
                                        <div><strong>Petugas 3:</strong> ${props.technician_3}</div>
                                    </div>
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
            });
        </script>
    </div>
</div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\Users\BPS 1900\Documents\timcare\resources\views/piket/view.blade.php ENDPATH**/ ?>