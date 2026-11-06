<x-app-layout>
<div class="min-h-screen">
    <div class="p-5 sm:p-7.5 lg:p-9">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Jadwal Piket Mingguan</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Lihat jadwal piket tim IT untuk setiap minggu</p>
        </div>

        @if (count($schedules) === 0)
            <div class="mb-4 rounded-lg border border-yellow-400 bg-yellow-100 p-4 text-yellow-700 dark:border-yellow-500/30 dark:bg-yellow-500/15 dark:text-yellow-400">
                <p class="font-semibold">Belum ada jadwal piket.</p>
                <p class="text-sm mt-1">Jadwal piket belum dibuat oleh admin.</p>
            </div>
        @endif

        @php
            $today = \Carbon\Carbon::now();

            $currentSchedule = collect($schedules)->first(function ($schedule) use ($today) {
                $start = \Carbon\Carbon::parse($schedule->week_start_date)->startOfDay();
                $end = $schedule->week_end_date
                    ? \Carbon\Carbon::parse($schedule->week_end_date)->endOfDay()
                    : $start->copy()->addDays(6)->endOfDay();
                return $today->between($start, $end);
            });

            $nextSchedule = collect($schedules)
                ->filter(function ($schedule) use ($today) {
                    return \Carbon\Carbon::parse($schedule->week_start_date)->startOfDay()->isAfter($today);
                })
                ->sortBy(function ($schedule) {
                    return \Carbon\Carbon::parse($schedule->week_start_date)->toDateString();
                })
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
        @endphp

        <div class="grid w-full gap-4 mb-6">
            <div class="w-full rounded-3xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-dark-800">
                <p class="text-sm uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400">Ringkasan Piket</p>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-3xl bg-gray-50 p-4 dark:bg-gray-900">
                        <p class="text-xs uppercase tracking-[0.25em] text-gray-500 dark:text-gray-400">Minggu Aktif</p>
                        @if ($currentSchedule)
                            @php
                                $activeStart = \Carbon\Carbon::parse($currentSchedule->week_start_date);
                                $activeEnd = $currentSchedule->week_end_date
                                    ? \Carbon\Carbon::parse($currentSchedule->week_end_date)
                                    : $activeStart->copy()->addDays(6);
                                $currentTechs = array_filter(array_map('trim', [$currentSchedule->technician_1, $currentSchedule->technician_2, $currentSchedule->technician_3]));
                            @endphp
                            <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">{{ $activeStart->format('d M') }} — {{ $activeEnd->format('d M Y') }}</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-200">Petugas: {{ implode(', ', $currentTechs) }}</p>
                        @else
                            <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">Tidak ada jadwal</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Silakan hubungi admin untuk jadwal piket.</p>
                        @endif
                    </div>
                    <div class="rounded-3xl bg-gray-50 p-4 dark:bg-gray-900">
                        <p class="text-xs uppercase tracking-[0.25em] text-gray-500 dark:text-gray-400">Jadwal Selanjutnya</p>
                        @if ($nextSchedule)
                            @php
                                $nextStart = \Carbon\Carbon::parse($nextSchedule->week_start_date);
                                $nextEnd = $nextSchedule->week_end_date
                                    ? \Carbon\Carbon::parse($nextSchedule->week_end_date)
                                    : $nextStart->copy()->addDays(6);
                                $nextTechs = array_filter(array_map('trim', [$nextSchedule->technician_1, $nextSchedule->technician_2, $nextSchedule->technician_3]));
                            @endphp
                            <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">{{ $nextStart->format('d M') }} — {{ $nextEnd->format('d M Y') }}</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-200">Petugas: {{ implode(', ', $nextTechs) }}</p>
                        @else
                            <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">Tidak ada jadwal berikutnya</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Semua jadwal sudah terisi.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <section class="mb-8 rounded-3xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-dark-800 sm:p-6">
            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Tugas Teknisi Saat Piket</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Tanggung jawab yang dijalankan selama periode piket.</p>
                </div>
                <span class="inline-flex w-fit items-center gap-2 rounded-full bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                    <span class="h-2 w-2 rounded-full bg-brand-500"></span>
                    5 jenis tugas
                </span>
            </div>
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                <article class="group flex h-full gap-4 rounded-2xl border border-gray-200 bg-gray-50/70 p-4 transition hover:-translate-y-0.5 hover:border-yellow-300 hover:bg-yellow-50/70 hover:shadow-sm dark:border-gray-700 dark:bg-gray-900/40 dark:hover:border-yellow-800 dark:hover:bg-yellow-900/10">
                    <span class="mt-0.5 flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-yellow-100 text-yellow-700 ring-1 ring-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300 dark:ring-yellow-800">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="6" width="12" height="12" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="m15 10 6-3v10l-6-3"/></svg>
                    </span>
                    <div class="min-w-0">
                        <h3 class="text-sm font-semibold leading-5 text-gray-900 dark:text-white">Penyiapan Video Conference</h3>
                        <p class="mt-1.5 text-sm leading-6 text-gray-600 dark:text-gray-400">Siapkan perangkat, koneksi, dan pengaturan audio/video untuk pertemuan online.</p>
                    </div>
                </article>
                <article class="group flex h-full gap-4 rounded-2xl border border-gray-200 bg-gray-50/70 p-4 transition hover:-translate-y-0.5 hover:border-blue-300 hover:bg-blue-50/70 hover:shadow-sm dark:border-gray-700 dark:bg-gray-900/40 dark:hover:border-blue-800 dark:hover:bg-blue-900/10">
                    <span class="mt-0.5 flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-700 ring-1 ring-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:ring-blue-800">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3 5 6v5c0 4.5 2.9 8.3 7 10 4.1-1.7 7-5.5 7-10V6l-7-3Z"/><path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4"/></svg>
                    </span>
                    <div class="min-w-0">
                        <h3 class="text-sm font-semibold leading-5 text-gray-900 dark:text-white">Troubleshooting Jaringan &amp; Keamanan</h3>
                        <p class="mt-1.5 text-sm leading-6 text-gray-600 dark:text-gray-400">Tangani gangguan jaringan, konfigurasi keamanan, dan akses sistem.</p>
                    </div>
                </article>
                <article class="group flex h-full gap-4 rounded-2xl border border-gray-200 bg-gray-50/70 p-4 transition hover:-translate-y-0.5 hover:border-green-300 hover:bg-green-50/70 hover:shadow-sm dark:border-gray-700 dark:bg-gray-900/40 dark:hover:border-green-800 dark:hover:bg-green-900/10">
                    <span class="mt-0.5 flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-green-100 text-green-700 ring-1 ring-green-200 dark:bg-green-900/30 dark:text-green-300 dark:ring-green-800">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="13" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 21h8m-4-4v4"/></svg>
                    </span>
                    <div class="min-w-0">
                        <h3 class="text-sm font-semibold leading-5 text-gray-900 dark:text-white">Troubleshooting Aset TIK</h3>
                        <p class="mt-1.5 text-sm leading-6 text-gray-600 dark:text-gray-400">Perbaiki masalah hardware atau software pada perangkat TIK.</p>
                    </div>
                </article>
                <article class="group flex h-full gap-4 rounded-2xl border border-gray-200 bg-gray-50/70 p-4 transition hover:-translate-y-0.5 hover:border-red-300 hover:bg-red-50/70 hover:shadow-sm dark:border-gray-700 dark:bg-gray-900/40 dark:hover:border-red-800 dark:hover:bg-red-900/10">
                    <span class="mt-0.5 flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-700 ring-1 ring-red-200 dark:bg-red-900/30 dark:text-red-300 dark:ring-red-800">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v13H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 3v6h5m-9 5 2 2 4-4"/></svg>
                    </span>
                    <div class="min-w-0">
                        <h3 class="text-sm font-semibold leading-5 text-gray-900 dark:text-white">Operator Rilis BRS</h3>
                        <p class="mt-1.5 text-sm leading-6 text-gray-600 dark:text-gray-400">Bantu proses rilis BRS bulanan dan pastikan operasional berjalan lancar.</p>
                    </div>
                </article>
                <article class="group flex h-full gap-4 rounded-2xl border border-gray-200 bg-gray-50/70 p-4 transition hover:-translate-y-0.5 hover:border-indigo-300 hover:bg-indigo-50/70 hover:shadow-sm dark:border-gray-700 dark:bg-gray-900/40 dark:hover:border-indigo-800 dark:hover:bg-indigo-900/10">
                    <span class="mt-0.5 flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700 ring-1 ring-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-300 dark:ring-indigo-800">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><circle cx="6" cy="6" r="2"/><circle cx="18" cy="6" r="2"/><circle cx="12" cy="18" r="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 6h8m-9.2 1.6 4 8m6.4-8-4 8"/></svg>
                    </span>
                    <div class="min-w-0">
                        <h3 class="text-sm font-semibold leading-5 text-gray-900 dark:text-white">Pemasangan Infrastruktur</h3>
                        <p class="mt-1.5 text-sm leading-6 text-gray-600 dark:text-gray-400">Pasang dan verifikasi jaringan, perangkat, serta aspek keamanan TI.</p>
                    </div>
                </article>
            </div>
        </section>

        <!-- Calendar Section -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-dark-800">
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">📅 Kalender Jadwal Piket</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Lihat semua jadwal piket dalam sebulan</p>
                </div>
                <span class="inline-flex items-center rounded-full bg-brand-50 px-3 py-1 text-sm font-semibold text-brand-700 dark:bg-brand-900/30 dark:text-brand-300">
                    {{ count($schedules) }} jadwal
                </span>
            </div>

            <div class="min-h-[520px] w-full overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm dark:border-gray-800 dark:bg-dark-900" id="piketCalendar"></div>
        </div>

        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
        <style>
            #piketCalendar { min-height: 520px; }
            #piketCalendar .fc { min-height: 100%; }
            #piketCalendar .fc .fc-toolbar {
                padding: 1rem 0;
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            #piketCalendar .fc .fc-toolbar-title { 
                font-size: 1.125rem; 
                font-weight: 700;
                color: #111827;
            }
            #piketCalendar .fc .fc-button-primary {
                background-color: #8b5cf6 !important;
                border-color: #8b5cf6 !important;
                font-weight: 500;
                padding: 0.5rem 0.75rem;
            }
            #piketCalendar .fc .fc-button-primary:not(:disabled).fc-button-active {
                background-color: #7c3aed !important;
                border-color: #7c3aed !important;
            }
            #piketCalendar .fc .fc-button-primary:hover {
                background-color: #7c3aed !important;
            }
            #piketCalendar .fc .fc-daygrid {
                border-color: #f3f4f6;
            }
            #piketCalendar .fc .fc-daygrid-day {
                padding: 0.5rem;
            }
            #piketCalendar .fc .fc-daygrid-day-top { 
                padding: 0.5rem 0.5rem 0.25rem;
            }
            #piketCalendar .fc .fc-daygrid-event { 
                padding: 0.4rem 0.5rem; 
                font-size: 0.75rem; 
                border-radius: 0.5rem;
                font-weight: 500;
                line-height: 1.2;
            }
            #piketCalendar .fc .fc-daygrid-more-link { 
                font-size: 0.75rem;
                color: #8b5cf6;
                font-weight: 600;
            }
            #piketCalendar .fc .fc-event-title { 
                white-space: normal; 
                line-height: 1.15;
                font-weight: 600;
            }
            #piketCalendar .fc .fc-event-crew { 
                color: rgba(255,255,255,0.9); 
                font-size: 0.65rem; 
                line-height: 1.15;
                margin-top: 0.2rem;
            }
            #piketCalendar .fc .fc-col-header-cell {
                padding: 0.75rem 0.5rem;
                font-weight: 600;
                color: #374151;
            }
            #piketCalendar .fc .fc-daygrid-day-number {
                padding: 0.4rem 0.5rem;
            }
            #piketCalendar .fc .fc-daygrid-day.fc-day-other {
                background-color: #f9fafb;
            }
            #piketCalendar .fc .fc-daygrid-day.fc-day-today {
                background-color: #f0f9ff;
            }
            #piketCalendar .fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
                color: #8b5cf6;
                font-weight: 700;
            }
            @media (prefers-color-scheme: dark) {
                #piketCalendar .fc .fc-toolbar-title {
                    color: #f3f4f6;
                }
                #piketCalendar .fc .fc-col-header-cell {
                    color: #d1d5db;
                }
                #piketCalendar .fc .fc-daygrid {
                    border-color: #374151;
                }
                #piketCalendar .fc .fc-daygrid-day.fc-day-other {
                    background-color: #111827;
                }
                #piketCalendar .fc .fc-daygrid-day.fc-day-today {
                    background-color: #1e3a8a;
                }
            }
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
                    events: @json($calendarEvents),
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
</x-app-layout>