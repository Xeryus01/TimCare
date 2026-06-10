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
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Manajemen Jadwal Piket Mingguan</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola jadwal piket tim IT untuk setiap minggu</p>
        </div>

        <?php if(session('success')): ?>
            <div class="mb-4 rounded-lg border border-green-400 bg-green-100 p-4 text-green-700 dark:border-green-500/30 dark:bg-green-500/15 dark:text-green-400">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if(count($schedules) === 0): ?>
            <div class="mb-4 rounded-lg border border-yellow-400 bg-yellow-100 p-4 text-yellow-700 dark:border-yellow-500/30 dark:bg-yellow-500/15 dark:text-yellow-400">
                <p class="font-semibold">Belum ada jadwal piket.</p>
                <p class="text-sm mt-1">Mulai dengan menambahkan jadwal piket baru menggunakan tombol "+ Tambah Jadwal".</p>
            </div>
        <?php endif; ?>

        <?php
            $calendarEvents = collect($schedules)->map(function ($schedule) {
                $start = \Carbon\Carbon::parse($schedule->week_start_date);
                $end = $schedule->week_end_date
                    ? \Carbon\Carbon::parse($schedule->week_end_date)
                    : $start->copy()->addDays(6);
                return [
                    'title' => "Piket: {$start->format('d/m')} - {$end->format('d/m')}",
                    'start' => $start->toDateString(),
                    'end' => $end->copy()->addDay()->toDateString(), // FullCalendar end is exclusive
                    'url' => route('piket.edit', $start->toDateString()),
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

            $currentSchedule = collect($schedules)->first(function ($schedule) {
                $start = \Carbon\Carbon::parse($schedule->week_start_date);
                $end = $schedule->week_end_date
                    ? \Carbon\Carbon::parse($schedule->week_end_date)
                    : $start->copy()->addDays(6);
                return now()->between($start->startOfDay(), $end->endOfDay());
            });
        ?>

        <div class="grid gap-3 md:grid-cols-2 mb-5">
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-dark-800">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-[0.25em] text-gray-500 dark:text-gray-400">Total Jadwal</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white"><?php echo e($schedules->count()); ?></p>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-1 text-xs font-semibold text-brand-700"><?php echo e($schedules->count()); ?></span>
                </div>
                <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">Jumlah jadwal piket tersimpan.</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-dark-800">
                <p class="text-xs uppercase tracking-[0.25em] text-gray-500 dark:text-gray-400">Minggu Aktif</p>
                <?php if($currentSchedule): ?>
                    <?php
                        $currentStart = \Carbon\Carbon::parse($currentSchedule->week_start_date);
                        $currentEnd = $currentSchedule->week_end_date
                            ? \Carbon\Carbon::parse($currentSchedule->week_end_date)
                            : $currentStart->copy()->addDays(6);
                    ?>
                    <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white"><?php echo e($currentStart->format('d M')); ?> — <?php echo e($currentEnd->format('d M Y')); ?></p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"><?php echo e($currentSchedule->technician_1); ?>, <?php echo e($currentSchedule->technician_2); ?>, <?php echo e($currentSchedule->technician_3); ?></p>
                <?php else: ?>
                    <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">Tidak aktif</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tambahkan jadwal minggu ini.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-dark-800">
            <div class="mb-6 flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400">Kalender Piket</p>
                    <h2 class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">Jadwal Mingguan</h2>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                        <span class="h-2.5 w-2.5 rounded-full bg-brand-500"></span>
                        <?php echo e($schedules->count()); ?> jadwal tersedia
                    </span>
                    <a href="<?php echo e(route('piket.create')); ?>" class="inline-flex items-center rounded-full bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">+ Tambah Jadwal</a>
                </div>
            </div>

            <div class="min-h-[580px] w-full overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm dark:border-gray-800 dark:bg-dark-900" id="piketCalendar"></div>
        </div>

        <div class="mt-6 rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-dark-800">
            <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400">Daftar Jadwal Piket</p>
                    <h2 class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">Data Mingguan</h2>
                </div>
                <span class="inline-flex items-center rounded-full bg-brand-50 px-3 py-1 text-sm font-semibold text-brand-700"><?php echo e($schedules->count()); ?> jadwal</span>
            </div>

            <div class="overflow-x-auto rounded-3xl border border-gray-100 bg-white shadow-sm dark:border-gray-800 dark:bg-dark-900">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Periode</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Petugas 1</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Petugas 2</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Petugas 3</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white dark:divide-gray-800 dark:bg-dark-900">
                        <?php $__currentLoopData = $schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $start = \Carbon\Carbon::parse($schedule->week_start_date);
                                $end = $schedule->week_end_date
                                    ? \Carbon\Carbon::parse($schedule->week_end_date)
                                    : $start->copy()->addDays(6);
                                $isCurrent = now()->between($start->startOfDay(), $end->endOfDay());
                            ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-900 <?php echo e($isCurrent ? 'bg-brand-50 dark:bg-brand-500/10' : ''); ?>">
                                <td class="px-4 py-4 align-top">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white"><?php echo e($start->format('d M')); ?> — <?php echo e($end->format('d M')); ?></div>
                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400"><?php echo e($start->format('d M Y')); ?> sampai <?php echo e($end->format('d M Y')); ?></div>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <div class="rounded-2xl bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700 dark:bg-gray-900 dark:text-gray-200"><?php echo e($schedule->technician_1); ?></div>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <div class="rounded-2xl bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700 dark:bg-gray-900 dark:text-gray-200"><?php echo e($schedule->technician_2); ?></div>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <div class="rounded-2xl bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700 dark:bg-gray-900 dark:text-gray-200"><?php echo e($schedule->technician_3); ?></div>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <?php if($isCurrent): ?>
                                        <span class="inline-flex items-center rounded-full bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-700">Minggu ini</span>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Terjadwal</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="<?php echo e(route('piket.edit', $schedule->week_start_date)); ?>" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-dark-800 dark:text-gray-200 dark:hover:bg-gray-900">Edit</a>
                                        <form method="POST" action="<?php echo e(route('piket.destroy', $schedule->week_start_date)); ?>" onsubmit="return confirm('Hapus jadwal piket ini? Tindakan ini tidak dapat dibatalkan.')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="inline-flex items-center rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-700">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>

        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
        <style>
            #piketCalendar { min-height: 580px; }
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
                        info.el.style.cursor = 'pointer';
                    },
                    eventClick: function(info) {
                        if (info.event.url) {
                            info.jsEvent.preventDefault();
                            window.location.href = info.event.url;
                        }
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
<?php endif; ?>
<?php /**PATH C:\Users\BPS 1900\Documents\timcare\resources\views/admin/piket/index.blade.php ENDPATH**/ ?>