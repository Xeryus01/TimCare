<x-app-layout>
<div class="min-h-full">
    <div class="mx-auto w-full max-w-[1680px] p-4 sm:p-6 lg:p-8">
        <div class="mb-7 flex flex-col gap-4 rounded-3xl border border-gray-200 bg-gradient-to-br from-white via-white to-brand-50/70 p-5 shadow-sm dark:border-gray-700 dark:from-dark-800 dark:via-dark-800 dark:to-dark-900 sm:flex-row sm:items-center sm:justify-between sm:p-7">
            <div class="flex items-start gap-4">
                <div class="hidden h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-brand-100 text-brand-700 dark:bg-brand-500/15 dark:text-brand-300 sm:flex">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                        <path d="M16 3v4M8 3v4M3 10h18M8 14h3M8 17h3"></path>
                    </svg>
                </div>
                <div>
                    <p class="mb-1 text-xs font-semibold uppercase tracking-[0.18em] text-brand-700 dark:text-brand-300">Operasional TI</p>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-3xl">Jadwal Piket</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Atur petugas dan pantau jadwal piket mingguan tim TI.</p>
                </div>
            </div>
            <a href="{{ route('piket.create') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-4 focus:ring-brand-500/20">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"></path></svg>
                Tambah Jadwal
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-lg border border-green-400 bg-green-100 p-4 text-green-700 dark:border-green-500/30 dark:bg-green-500/15 dark:text-green-400">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded-lg border border-amber-400 bg-amber-100 p-4 text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/15 dark:text-amber-300">
                {{ session('error') }}
            </div>
        @endif

        @if (count($schedules) === 0)
            <div class="mb-4 rounded-lg border border-yellow-400 bg-yellow-100 p-4 text-yellow-700 dark:border-yellow-500/30 dark:bg-yellow-500/15 dark:text-yellow-400">
                <p class="font-semibold">Belum ada jadwal piket.</p>
                <p class="text-sm mt-1">Mulai dengan menambahkan jadwal piket baru menggunakan tombol "+ Tambah Jadwal".</p>
            </div>
        @endif

        @php
            $calendarEvents = collect($schedules)->map(function ($schedule) {
                $start = \Carbon\Carbon::parse($schedule->week_start_date);
                $end = $schedule->week_end_date
                    ? \Carbon\Carbon::parse($schedule->week_end_date)
                    : $start->copy()->addDays(6);
                $isPast = $schedule->hasPassed();
                return [
                    'title' => "Piket: {$start->format('d/m')} - {$end->format('d/m')}",
                    'start' => $start->toDateString(),
                    'end' => $end->copy()->addDay()->toDateString(), // FullCalendar end is exclusive
                    'url' => $isPast ? null : route('piket.edit', $schedule),
                    'allDay' => true,
                    'backgroundColor' => $isPast ? '#94a3b8' : '#8b5cf6',
                    'borderColor' => $isPast ? '#94a3b8' : '#8b5cf6',
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
        @endphp

        <div class="mb-6 grid gap-4 md:grid-cols-2">
            <div class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-gray-700 dark:bg-dark-800">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total jadwal tersimpan</p>
                        <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $schedules->count() }}</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-300">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M16 3v4M8 3v4M3 10h18"></path></svg>
                    </span>
                </div>
                <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">Jumlah seluruh periode piket yang sudah dibuat.</p>
            </div>
            <div class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-gray-700 dark:bg-dark-800">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Jadwal saat ini</p>
                @if ($currentSchedule)
                    @php
                        $currentStart = \Carbon\Carbon::parse($currentSchedule->week_start_date);
                        $currentEnd = $currentSchedule->week_end_date
                            ? \Carbon\Carbon::parse($currentSchedule->week_end_date)
                            : $currentStart->copy()->addDays(6);
                    @endphp
                    <p class="mt-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $currentStart->format('d M') }} — {{ $currentEnd->format('d M Y') }}</p>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">{{ $currentSchedule->technician_1 }} · {{ $currentSchedule->technician_2 }} · {{ $currentSchedule->technician_3 }}</p>
                @else
                    <p class="mt-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">Belum ada jadwal aktif</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Buat jadwal baru untuk menetapkan petugas piket.</p>
                @endif
                    </div>
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $currentSchedule ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-300' }}">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="m8 12 2.5 2.5L16 9"></path></svg>
                    </span>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-dark-800">
            <div class="flex flex-col gap-3 border-b border-gray-100 px-5 py-5 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Kalender jadwal</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pilih jadwal yang akan datang atau sedang berjalan untuk mengeditnya.</p>
                </div>
            </div>

            <div class="p-3 sm:p-5">
                <div class="min-h-[440px] w-full overflow-hidden rounded-2xl border border-gray-100 bg-white dark:border-gray-800 dark:bg-dark-900 sm:min-h-[500px]" id="piketCalendar"></div>
            </div>
        </div>

        <div class="mt-6 overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-dark-800">
            <div class="flex flex-col gap-4 border-b border-gray-100 px-5 py-5 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar jadwal</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tinjau periode, petugas, dan status jadwal.</p>
                </div>
                <form method="GET" class="flex items-center gap-2">
                    <label for="rangeSelect" class="text-sm font-medium text-gray-600 dark:text-gray-300">Periode</label>
                    <select id="rangeSelect" name="range" onchange="this.form.submit()" class="rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-dark-900 dark:text-gray-200">
                        <option value="1" @selected($range === '1')>Per bulan</option>
                        <option value="2" @selected($range === '2')>Per 2 bulan</option>
                        <option value="3" @selected($range === '3')>Per 3 bulan</option>
                        <option value="all" @selected($range === 'all')>Semua</option>
                    </select>
                    <noscript><button type="submit" class="rounded-lg bg-brand-600 px-3 py-1.5 text-sm font-semibold text-white">Terapkan</button></noscript>
                </form>
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
                        @forelse ($scheduleList as $schedule)
                            @php
                                $start = \Carbon\Carbon::parse($schedule->week_start_date);
                                $end = $schedule->week_end_date
                                    ? \Carbon\Carbon::parse($schedule->week_end_date)
                                    : $start->copy()->addDays(6);
                                $isCurrent = now()->between($start->startOfDay(), $end->endOfDay());
                                $isPast = $schedule->hasPassed();
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-900 {{ $isCurrent ? 'bg-brand-50 dark:bg-brand-500/10' : '' }}">
                                <td class="px-4 py-4 align-top">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $start->format('d M') }} — {{ $end->format('d M') }}</div>
                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $start->format('d M Y') }} sampai {{ $end->format('d M Y') }}</div>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <div class="rounded-2xl bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700 dark:bg-gray-900 dark:text-gray-200">{{ $schedule->technician_1 }}</div>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <div class="rounded-2xl bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700 dark:bg-gray-900 dark:text-gray-200">{{ $schedule->technician_2 }}</div>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <div class="rounded-2xl bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700 dark:bg-gray-900 dark:text-gray-200">{{ $schedule->technician_3 }}</div>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    @if ($isCurrent)
                                        <span class="inline-flex items-center rounded-full bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-700">Minggu ini</span>
                                    @elseif ($isPast)
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-500 dark:bg-gray-800 dark:text-gray-400">Terlewat</span>
                                    @else
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Terjadwal</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <div class="flex flex-wrap gap-2">
                                        @if ($isPast)
                                            <span class="inline-flex items-center rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-500 dark:bg-gray-800 dark:text-gray-400" title="Jadwal yang sudah terlewat tidak dapat diedit">Terkunci</span>
                                        @else
                                            <a href="{{ route('piket.edit', $schedule) }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-dark-800 dark:text-gray-200 dark:hover:bg-gray-900">Edit</a>
                                        @endif
                                        <form method="POST" action="{{ route('piket.destroy', $schedule) }}" onsubmit="return confirm('Hapus jadwal piket ini? Tindakan ini tidak dapat dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-700">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada jadwal piket pada rentang ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($scheduleList instanceof \Illuminate\Contracts\Pagination\Paginator)
                <div class="mt-4">
                    <x-pagination :paginator="$scheduleList" />
                </div>
            @endif
        </div>

        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
        <style>
            #piketCalendar { min-height: 440px; }
            #piketCalendar .fc { min-height: 100%; }
            #piketCalendar .fc .fc-toolbar { gap: 0.75rem; }
            #piketCalendar .fc .fc-toolbar-title { color: #111827; font-size: 1.05rem; font-weight: 700; }
            #piketCalendar .fc .fc-button-primary { border-color: #0284c7; border-radius: 0.65rem; background: #0284c7; box-shadow: none; font-size: 0.8rem; font-weight: 600; }
            #piketCalendar .fc .fc-button-primary:hover { border-color: #0369a1; background: #0369a1; }
            #piketCalendar .fc .fc-button-primary:focus { box-shadow: 0 0 0 3px rgb(14 165 233 / 20%); }
            #piketCalendar .fc .fc-button-primary:disabled { border-color: #7dd3fc; background: #7dd3fc; }
            #piketCalendar .fc .fc-daygrid-day-frame { min-height: 5.5rem; }
            #piketCalendar .fc .fc-col-header-cell { background: #f9fafb; color: #4b5563; font-size: 0.75rem; font-weight: 600; }
            #piketCalendar .fc .fc-daygrid-day-top { padding: 0.55rem 0.65rem; }
            #piketCalendar .fc .fc-daygrid-event { padding: 0.4rem 0.45rem; font-size: 0.75rem; border-radius: 0.55rem; }
            #piketCalendar .fc .fc-daygrid-more-link { font-size: 0.75rem; }
            #piketCalendar .fc .fc-event-title { white-space: normal; line-height: 1.15; }
            #piketCalendar .fc .fc-event-crew { color: rgba(255,255,255,0.85); font-size: 0.65rem; line-height: 1.15; }
            .dark #piketCalendar .fc { --fc-border-color: #334155; --fc-page-bg-color: #0f172a; --fc-neutral-bg-color: #1e293b; --fc-today-bg-color: rgb(14 165 233 / 10%); color: #e5e7eb; }
            .dark #piketCalendar .fc .fc-toolbar-title { color: #f9fafb; }
            .dark #piketCalendar .fc .fc-col-header-cell { background: #1e293b; color: #d1d5db; }
            .dark #piketCalendar .fc .fc-daygrid-day-number { color: #d1d5db; }
            .dark #piketCalendar .fc .fc-button-primary { color: #fff; }
            @media (max-width: 640px) {
                #piketCalendar { min-height: 400px; }
                #piketCalendar .fc .fc-toolbar { flex-direction: column; }
                #piketCalendar .fc .fc-toolbar-chunk { display: flex; justify-content: center; }
                #piketCalendar .fc .fc-toolbar-title { font-size: 0.95rem; }
                #piketCalendar .fc .fc-button-primary { padding: 0.35rem 0.45rem; font-size: 0.7rem; }
                #piketCalendar .fc .fc-daygrid-day-frame { min-height: 4rem; }
                #piketCalendar .fc .fc-daygrid-day-top { padding: 0.3rem; }
                #piketCalendar .fc .fc-daygrid-event { padding: 0.2rem; font-size: 0.65rem; }
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
                        info.el.style.cursor = info.event.url ? 'pointer' : 'default';
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
</x-app-layout>
