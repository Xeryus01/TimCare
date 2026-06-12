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
                    : $start->copy()->endOfDay();
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

        <!-- Current & Next Week Cards -->
        <div class="mb-8 grid gap-6 md:grid-cols-2">
            <!-- Current Week -->
            <div class="relative overflow-hidden rounded-2xl border border-blue-200 bg-gradient-to-br from-blue-50 to-blue-100 p-6 shadow-sm dark:border-blue-900/30 dark:from-blue-900/20 dark:to-blue-900/10">
                <div class="absolute -right-12 -top-12 h-32 w-32 bg-blue-200 opacity-10 rounded-full dark:bg-blue-400/10"></div>
                <div class="relative z-10">
                    <div class="inline-flex items-center gap-2 rounded-full bg-blue-600 px-3 py-1 text-xs font-semibold text-white mb-3">
                        <svg class="h-4 w-4 animate-pulse" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="8"/></svg>
                        SEDANG BERLANGSUNG
                    </div>
                    <p class="text-sm uppercase tracking-widest text-blue-700 dark:text-blue-300 font-semibold">Minggu Piket Aktif</p>
                    @if ($currentSchedule)
                        @php
                            $activeStart = \Carbon\Carbon::parse($currentSchedule->week_start_date);
                            $activeEnd = $currentSchedule->week_end_date
                                ? \Carbon\Carbon::parse($currentSchedule->week_end_date)
                                : $activeStart->copy()->addDays(6);
                        @endphp
                        <p class="mt-3 text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $activeStart->format('d M') }} — {{ $activeEnd->format('d M Y') }}</p>
                        <div class="mt-4 space-y-2">
                            <p class="text-xs text-blue-700 dark:text-blue-300">Petugas yang Bertugas:</p>
                            <div class="flex flex-wrap gap-2">
                                @php $techs = array_filter([$currentSchedule->technician_1, $currentSchedule->technician_2, $currentSchedule->technician_3]) @endphp
                                @if (count($techs) > 0)

                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white">
                                    <span class="h-2 w-2 rounded-full bg-blue-200"></span>
                                    {{ $currentSchedule->technician_1 }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white">
                                    <span class="h-2 w-2 rounded-full bg-blue-200"></span>
                                    {{ $currentSchedule->technician_2 }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white">
                                    <span class="h-2 w-2 rounded-full bg-blue-200"></span>
                                    {{ $currentSchedule->technician_3 }}
                                </span>

                                @else
                                    <p class="text-sm text-blue-600 dark:text-blue-300 italic">Petugas belum ditentukan</p>
                                @endif
                            </div>
                        </div>
                    @else
                        <p class="mt-3 text-2xl font-bold text-blue-900 dark:text-blue-100">Tidak ada jadwal aktif</p>
                        <p class="mt-2 text-sm text-blue-700 dark:text-blue-300">Silakan hubungi admin untuk jadwal piket minggu ini.</p>
                    @endif
                </div>
            </div>

            <!-- Next Week -->
            <div class="relative overflow-hidden rounded-2xl border border-purple-200 bg-gradient-to-br from-purple-50 to-purple-100 p-6 shadow-sm dark:border-purple-900/30 dark:from-purple-900/20 dark:to-purple-900/10">
                <div class="absolute -right-12 -top-12 h-32 w-32 bg-purple-200 opacity-10 rounded-full dark:bg-purple-400/10"></div>
                <div class="relative z-10">
                    <div class="inline-flex items-center gap-2 rounded-full bg-purple-600 px-3 py-1 text-xs font-semibold text-white mb-3">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M6 2a1 1 0 000 2h8a1 1 0 100-2H6z"/><path fill-rule="evenodd" d="M6 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H8a2 2 0 01-2-2V4zm2 2h8v10H8V6z" clip-rule="evenodd"/></svg>
                        JADWAL BERIKUTNYA
                    </div>
                    <p class="text-sm uppercase tracking-widest text-purple-700 dark:text-purple-300 font-semibold">Minggu Depan</p>
                    @if ($nextSchedule)
                        @php
                            $nextStart = \Carbon\Carbon::parse($nextSchedule->week_start_date);
                            $nextEnd = $nextSchedule->week_end_date
                                ? \Carbon\Carbon::parse($nextSchedule->week_end_date)
                                : $nextStart->copy()->addDays(6);
                        @endphp
                        <p class="mt-3 text-2xl font-bold text-purple-900 dark:text-purple-100">{{ $nextStart->format('d M') }} — {{ $nextEnd->format('d M Y') }}</p>
                        <div class="mt-4 space-y-2">
                            <p class="text-xs text-purple-700 dark:text-purple-300">Petugas yang Ditugaskan:</p>
                            <div class="flex flex-wrap gap-2">
                                @php $nextTechs = array_filter([$nextSchedule->technician_1, $nextSchedule->technician_2, $nextSchedule->technician_3]) @endphp
                                @if (count($nextTechs) > 0)
                                    @foreach ($nextTechs as $tech)
                                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-purple-600 px-3 py-1.5 text-sm font-medium text-white">
                                            <span class="h-2 w-2 rounded-full bg-purple-200"></span>
                                            {{ $tech }}
                                        </span>
                                    @endforeach
                                @else
                                    <p class="text-sm text-purple-600 dark:text-purple-300 italic">Petugas belum ditentukan</p>
                                @endif
                            </div>
                        </div>
                    @else
                        <p class="mt-3 text-2xl font-bold text-purple-900 dark:text-purple-100">Tidak ada jadwal berikutnya</p>
                        <p class="mt-2 text-sm text-purple-700 dark:text-purple-300">Semua jadwal piket sudah terisi untuk bulan ini.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Duties Section -->
        <div class="mb-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-dark-800">
            <div class="mb-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">📋 Tugas Teknisi Saat Piket</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Tanggung jawab yang harus dijalankan selama periode piket</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="group rounded-xl border border-yellow-200 bg-gradient-to-br from-yellow-50 to-yellow-100/50 p-5 transition-all hover:shadow-md dark:border-yellow-900/30 dark:from-yellow-900/10 dark:to-yellow-900/5">
                    <div class="mb-3 inline-flex rounded-lg bg-yellow-600 p-2.5 text-white">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Penyiapan Video Conference</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Siapkan perangkat, koneksi, dan pengaturan audio/video untuk pertemuan online.</p>
                </div>
                <div class="group rounded-xl border border-blue-200 bg-gradient-to-br from-blue-50 to-blue-100/50 p-5 transition-all hover:shadow-md dark:border-blue-900/30 dark:from-blue-900/10 dark:to-blue-900/5">
                    <div class="mb-3 inline-flex rounded-lg bg-blue-600 p-2.5 text-white">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101a.75.75 0 10-1.06-1.06l-1.102 1.101a2.5 2.5 0 01-3.536-3.536l4-4a2.5 2.5 0 013.536 0l.707.707a.75.75 0 001.06-1.06l-.707-.707z" clip-rule="evenodd"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Troubleshooting Jaringan & Keamanan</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Tangani gangguan jaringan, konfigurasi keamanan, dan akses sistem.</p>
                </div>
                <div class="group rounded-xl border border-green-200 bg-gradient-to-br from-green-50 to-green-100/50 p-5 transition-all hover:shadow-md dark:border-green-900/30 dark:from-green-900/10 dark:to-green-900/5">
                    <div class="mb-3 inline-flex rounded-lg bg-green-600 p-2.5 text-white">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4z"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Troubleshooting Aset TIK</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Perbaiki masalah hardware atau software pada perangkat TIK.</p>
                </div>
                <div class="group rounded-xl border border-red-200 bg-gradient-to-br from-red-50 to-red-100/50 p-5 transition-all hover:shadow-md dark:border-red-900/30 dark:from-red-900/10 dark:to-red-900/5">
                    <div class="mb-3 inline-flex rounded-lg bg-red-600 p-2.5 text-white">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/><path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0015.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Operator Rilis BRS</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Bantu proses rilis BRS bulanan dan pastikan operasional berjalan lancar.</p>
                </div>
                <div class="group rounded-xl border border-indigo-200 bg-gradient-to-br from-indigo-50 to-indigo-100/50 p-5 transition-all hover:shadow-md dark:border-indigo-900/30 dark:from-indigo-900/10 dark:to-indigo-900/5">
                    <div class="mb-3 inline-flex rounded-lg bg-indigo-600 p-2.5 text-white">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000 2H3a1 1 0 000 2h12a1 1 0 100-2h-3a1 1 0 000-2 2 2 0 00-2 2V3h1a1 1 0 000-2H7a1 1 0 000 2h1v2a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2v-5h-2.586l.293-.293a1 1 0 00-1.414-1.414L13 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l.293.293H8v5a1 1 0 11-2 0V5z" clip-rule="evenodd"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Pemasangan Infrastruktur</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Pasang dan verifikasi jaringan, perangkat, serta aspek keamanan TI.</p>
                </div>
            </div>
        </div>

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