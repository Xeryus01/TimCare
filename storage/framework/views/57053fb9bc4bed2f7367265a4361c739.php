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
            'Batal' => \App\Models\Ticket::whereIn('status', [\App\Models\Ticket::STATUS_REJECTED, \App\Models\Ticket::STATUS_CANCELLED, 'Dibatalkan'])->count(),
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
        $zoomEvents = \App\Models\Reservation::with('requester')
            ->whereNotNull('zoom_link')
            ->where('zoom_link', '!=', '')
            ->where('start_time', '>=', now()->startOfMonth())
            ->where('end_time', '<=', now()->endOfMonth())
            ->get(['start_time', 'end_time', 'room_name', 'purpose', 'status', 'code', 'participants_count', 'operator_needed', 'breakroom_needed', 'requester_id', 'id']);

        // Data untuk kalender piket - semua jadwal piket dalam bulan ini
        $piketEvents = \App\Models\PiketSchedule::where('week_start_date', '>=', now()->startOfMonth()->toDateString())
            ->where('week_start_date', '<=', now()->endOfMonth()->toDateString())
            ->get(['id', 'week_start_date', 'technician_1', 'technician_2', 'technician_3']);

        $activePiketSchedules = $piketEvents->map(function ($schedule) {
            $weekStart = \Illuminate\Support\Carbon::parse($schedule->week_start_date)->startOfDay();
            $weekEnd = $weekStart->copy()->addDays(6)->endOfDay();
            $isCurrentWeek = now()->between($weekStart, $weekEnd);

            return [
                'week_start_date' => $weekStart->format('d M Y'),
                'week_end_date' => $weekEnd->format('d M Y'),
                'technicians' => array_filter([$schedule->technician_1, $schedule->technician_2, $schedule->technician_3]),
                'is_active' => $isCurrentWeek,
            ];
        })->filter(function ($schedule) {
            return $schedule['is_active'];
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
                    'requester' => optional($event->requester)->name,
                ]
            ];
        })->toArray();

        // Piket schedules are not added to the calendar events here.
        // The calendar will only display Zoom reservations; piket is shown in the Tim Piket card.
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
            'Batal' => \App\Models\Ticket::whereIn('status', [\App\Models\Ticket::STATUS_REJECTED, \App\Models\Ticket::STATUS_CANCELLED, 'Dibatalkan'])->count(),
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
        $zoomEvents = \App\Models\Reservation::with('requester')
            ->whereNotNull('zoom_link')
            ->where('zoom_link', '!=', '')
            ->where('start_time', '>=', now()->startOfMonth())
            ->where('end_time', '<=', now()->endOfMonth())
            ->get(['start_time', 'end_time', 'room_name', 'purpose', 'status', 'code', 'participants_count', 'operator_needed', 'breakroom_needed', 'requester_id', 'id']);

        $piketEvents = \App\Models\PiketSchedule::where('week_start_date', '>=', now()->startOfMonth()->toDateString())
            ->where('week_start_date', '<=', now()->endOfMonth()->toDateString())
            ->get(['id', 'week_start_date', 'technician_1', 'technician_2', 'technician_3']);

        $activePiketSchedules = $piketEvents->map(function ($schedule) {
            $weekStart = \Illuminate\Support\Carbon::parse($schedule->week_start_date)->startOfDay();
            $weekEnd = $weekStart->copy()->addDays(6)->endOfDay();
            $techs = array_filter([$schedule->technician_1, $schedule->technician_2, $schedule->technician_3]);

            return [
                'week_start_date' => $weekStart->format('d M Y'),
                'week_end_date' => $weekEnd->format('d M Y'),
                'technicians' => $techs,
                'is_active' => now()->between($weekStart, $weekEnd),
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
                    'requester' => optional($event->requester)->name,
                ]
            ];
        })->toArray();

        // Piket schedules are not added to the calendar for regular users; piket is shown in Tim Piket card only.
    }
?>

<style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
        --brand: #2563eb;
        --brand-50: #eff6ff;
        --brand-100: #dbeafe;
        --brand-dark: #1d4ed8;
        --slate-50: #f8fafc;
        --slate-100: #f1f5f9;
        --slate-200: #e2e8f0;
        --slate-300: #cbd5e1;
        --slate-400: #94a3b8;
        --slate-500: #64748b;
        --slate-600: #475569;
        --slate-700: #334155;
        --slate-800: #1e293b;
        --slate-900: #0f172a;
        --white: #ffffff;
        --red: #ef4444;
        --green: #22c55e;
        --amber: #f59e0b;
        --purple: #8b5cf6;
        --blue: #3b82f6;
        --radius: 14px;
        --radius-sm: 10px;
        --shadow-sm: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
        --shadow: 0 4px 16px rgba(0,0,0,.07);
    }

    .page-content {
        flex: 1; overflow-y: auto; padding: 24px;
        display: flex; flex-direction: column; gap: 20px;
    }

    .pg-head {
        display: flex; align-items: flex-start;
        justify-content: space-between; gap: 16px;
        flex-wrap: wrap;
    }
    .pg-head h1 { font-size: 1.4rem; font-weight: 800; color: var(--slate-900); line-height: 1.2; }
    .pg-head p { font-size: .825rem; color: var(--slate-500); margin-top: 4px; }
    .pg-actions { display: flex; gap: 8px; flex-wrap: wrap; }
    
    .btn-primary {
        display: inline-flex; align-items: center; gap: 6px;
        background: var(--brand); color: var(--white);
        font-size: .8rem; font-weight: 600; padding: 8px 16px;
        border-radius: var(--radius-sm); border: none; cursor: pointer;
        font-family: inherit; text-decoration: none;
        transition: all .15s; box-shadow: 0 2px 8px rgba(37,99,235,.25);
    }
    .btn-primary:hover { background: var(--brand-dark); transform: translateY(-1px); }
    
    .btn-outline {
        display: inline-flex; align-items: center; gap: 6px;
        background: var(--white); color: var(--brand);
        font-size: .8rem; font-weight: 600; padding: 8px 16px;
        border-radius: var(--radius-sm);
        border: 1.5px solid var(--brand); cursor: pointer;
        font-family: inherit; text-decoration: none;
        transition: all .15s;
    }
    .btn-outline:hover { background: var(--brand-50); }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
    }
    @media (max-width: 1100px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }

    .stat-card {
        background: var(--white);
        border: 1px solid var(--slate-200);
        border-radius: var(--radius);
        padding: 20px 22px;
        display: flex; align-items: center; gap: 16px;
        box-shadow: var(--shadow-sm);
        transition: all .2s;
        position: relative; overflow: hidden;
    }
    .stat-card::after {
        content: '';
        position: absolute; top: 0; left: 0; right: 0; height: 3px;
        border-radius: var(--radius) var(--radius) 0 0;
    }
    .stat-card.red::after { background: var(--red); }
    .stat-card.blue::after { background: var(--blue); }
    .stat-card.green::after { background: var(--green); }
    .stat-card.purple::after { background: var(--purple); }
    .stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow); }
    
    .stat-icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .stat-icon svg { width: 22px; height: 22px; }
    .stat-icon.red { background: #fef2f2; color: var(--red); }
    .stat-icon.blue { background: #eff6ff; color: var(--blue); }
    .stat-icon.green { background: #f0fdf4; color: var(--green); }
    .stat-icon.purple { background: #f5f3ff; color: var(--purple); }
    
    .stat-info { flex: 1; min-width: 0; }
    .stat-label { font-size: .75rem; color: var(--slate-500); font-weight: 500; margin-bottom: 4px; }
    .stat-value { font-size: 1.8rem; font-weight: 800; line-height: 1; }
    .stat-value.red { color: var(--red); }
    .stat-value.blue { color: var(--blue); }
    .stat-value.green { color: var(--green); }
    .stat-value.purple { color: var(--purple); }

    .card {
        background: var(--white);
        border: 1px solid var(--slate-200);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }
    .card-head {
        padding: 16px 20px;
        border-bottom: 1px solid var(--slate-100);
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px; flex-wrap: wrap;
    }
    .card-title { font-size: .925rem; font-weight: 700; color: var(--slate-900); }
    .card-sub { font-size: .75rem; color: var(--slate-500); margin-top: 2px; }
    .card-body { padding: 16px 20px; }
    .card-body.no-pad { padding: 0; }

    .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    @media (max-width: 900px) { .two-col { grid-template-columns: 1fr; } }

    .empty-state {
        display: flex; flex-direction: column;
        align-items: center; text-align: center;
        padding: 28px 20px; gap: 8px;
    }
    .empty-icon {
        width: 44px; height: 44px; border-radius: 12px;
        background: var(--slate-100);
        display: flex; align-items: center; justify-content: center;
        color: var(--slate-400); margin-bottom: 4px;
    }
    .empty-icon svg { width: 22px; height: 22px; }
    .empty-title { font-size: .875rem; font-weight: 600; color: var(--slate-700); }
    .empty-desc { font-size: .78rem; color: var(--slate-400); }

    #zoomCalendar {
        height: auto;
    }
    .fc .fc-button { font-size: .72rem !important; padding: .3rem .6rem !important; }
    .fc .fc-toolbar-title { font-size: .9rem !important; }
    .fc .fc-daygrid-day-top { padding: .3rem .4rem; }
    .fc .fc-col-header-cell { font-size: .72rem; }
    .fc .fc-daygrid-day-number { font-size: .75rem; }

    .chart-wrap { position: relative; height: 240px; padding: 4px 0; }

    .ticket-item {
        display: flex; align-items: center; gap: 12px;
        padding: 12px 20px;
        border-bottom: 1px solid var(--slate-100);
        transition: background .15s;
    }
    .ticket-item:last-child { border-bottom: none; }
    .ticket-item:hover { background: var(--slate-50); }
    .ticket-icon {
        width: 34px; height: 34px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; background: var(--brand-50); color: var(--brand);
    }
    .ticket-icon svg { width: 16px; height: 16px; }
    .ticket-meta { flex: 1; min-width: 0; }
    .ticket-title { font-size: .825rem; font-weight: 600; color: var(--slate-800); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .ticket-code { font-size: .7rem; color: var(--slate-400); margin-top: 2px; }
    .ticket-badge {
        font-size: .68rem; font-weight: 600; padding: 3px 10px;
        border-radius: 999px; white-space: nowrap;
        background: #f1f5f9; color: var(--slate-600);
    }
    .ticket-badge.open { background: #eff6ff; color: var(--blue); }
    .ticket-badge.assigned { background: #fff7ed; color: #c2410c; }
    .ticket-badge.waiting { background: #f5f3ff; color: #6d28d9; }
    .ticket-badge.done { background: #f0fdf4; color: var(--green); }
    .ticket-badge.cancelled { background: #fee2e2; color: var(--red); }

    .legend { display: flex; align-items: center; gap: 5px; font-size: .72rem; color: var(--slate-600); }
    .dot { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }

    select {
        font-size: .75rem; font-family: inherit; color: var(--slate-700);
        background: var(--white); border: 1px solid var(--slate-200);
        padding: 5px 10px; border-radius: 8px; cursor: pointer;
        outline: none; transition: border-color .15s;
    }
    select:focus { border-color: var(--brand); }

    .link { font-size: .75rem; font-weight: 600; color: var(--brand); text-decoration: none; }
    .link:hover { text-decoration: underline; }

    .dark .page-content,
    html.dark .page-content {
        color: #e2e8f0;
    }
    .dark .pg-head h1,
    html.dark .pg-head h1 { color: #ffffff; }
    .dark .pg-head p,
    html.dark .pg-head p { color: #94a3b8; }

    .dark .card,
    html.dark .card,
    .dark .stat-card,
    html.dark .stat-card {
        background: #111827;
        border-color: #374151;
        box-shadow: 0 10px 30px rgba(0,0,0,.4);
    }
    .dark .card-head,
    html.dark .card-head {
        border-bottom-color: #374151;
    }
    .dark .card-title,
    html.dark .card-title { color: #f8fafc; }
    .dark .card-sub,
    html.dark .card-sub { color: #94a3b8; }
    .dark .stat-label,
    html.dark .stat-label { color: #94a3b8; }

    .dark .ticket-item,
    html.dark .ticket-item {
        border-color: #374151;
        background: #111827;
    }
    .dark .ticket-item:hover,
    html.dark .ticket-item:hover { background: #1f2937; }
    .dark .ticket-icon,
    html.dark .ticket-icon { background: #1f2937; color: #93c5fd; }
    .dark .ticket-title,
    html.dark .ticket-title { color: #f8fafc; }
    .dark .ticket-code,
    html.dark .ticket-code { color: #94a3b8; }
    .dark .ticket-badge,
    html.dark .ticket-badge { background: #1f2937; color: #cbd5e1; }
    .dark .ticket-badge.open,
    html.dark .ticket-badge.open { background: #1e40af1a; color: #93c5fd; }
    .dark .ticket-badge.assigned,
    html.dark .ticket-badge.assigned { background: #7c2d12; color: #fbbf24; }
    .dark .ticket-badge.waiting,
    html.dark .ticket-badge.waiting { background: #5b21b6; color: #ddd6fe; }
    .dark .ticket-badge.done,
    html.dark .ticket-badge.done { background: #134e4a; color: #a7f3d0; }
    .dark .ticket-badge.cancelled,
    html.dark .ticket-badge.cancelled { background: #7f1d1d; color: #fecaca; }

    .dark .empty-icon,
    html.dark .empty-icon { background: #1f2937; color: #9ca3af; }
    .dark .empty-title,
    html.dark .empty-title { color: #f8fafc; }
    .dark .empty-desc,
    html.dark .empty-desc { color: #94a3b8; }

    .dark select,
    html.dark select {
        color: #e5e7eb;
        background: #1f2937;
        border-color: #4b5563;
    }

    .dark .link,
    html.dark .link { color: #93c5fd; }
</style>

<div class="page-content">

    <!-- Page Header -->
    <div class="pg-head">
        <div>
            <h1>Selamat datang, <?php echo e(auth()->user()->name); ?> 👋</h1>
            <p>Pantau tiket, pengajuan Zoom, dan performa tim hari ini.</p>
        </div>
        <div class="pg-actions">
            <a href="<?php echo e(route('tickets.create')); ?>" class="btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                Ajukan Tiket
            </a>
            <a href="<?php echo e(route('reservations.create')); ?>" class="btn-outline">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M8 12l3 3 5-5"/></svg>
                Ajukan Zoom
            </a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="stats-grid">
        <div class="stat-card red">
            <div class="stat-icon red">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/></svg>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Tiket Masalah</div>
                <div class="stat-value red"><?php echo e($totalTickets); ?></div>
            </div>
        </div>
        <div class="stat-card blue">
            <div class="stat-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Tiket Zoom</div>
                <div class="stat-value blue"><?php echo e($totalZooms); ?></div>
            </div>
        </div>
        <div class="stat-card green">
            <div class="stat-icon green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div class="stat-info">
                <div class="stat-label">Layanan Selesai</div>
                <div class="stat-value green"><?php echo e($layananSelesai); ?></div>
            </div>
        </div>
        <div class="stat-card purple">
            <div class="stat-icon purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            </div>
            <div class="stat-info">
                <div class="stat-label">Capaian</div>
                <div class="stat-value purple"><?php echo e($capaianPersentase); ?>%</div>
            </div>
        </div>
    </div>

    <!-- Piket + Calendar -->
    <div class="two-col">
        <!-- Tim Piket -->
        <div class="card">
            <div class="card-head">
                <div>
                    <div class="card-title">Tim Piket Aktif</div>
                    <div class="card-sub">Tim yang bertugas saat ini</div>
                </div>
                <a href="<?php echo e(route('piket.index')); ?>" class="link">Lihat semua</a>
            </div>
            <div class="card-body">
                <?php if($activePiketSchedules->isNotEmpty()): ?>
                    <?php $schedule = $activePiketSchedules->first(); ?>
                    <div class="mb-4 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-dark-800">
                        <div class="flex items-center justify-between gap-4">
                            <div class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                                <?php echo e($schedule['week_start_date']); ?> - <?php echo e($schedule['week_end_date']); ?>

                            </div>
                            <div class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-200">Aktif</div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $schedule['technicians']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $tech): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-dark-800">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <div class="text-xs uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400">Petugas <?php echo e($index + 1); ?></div>
                                        <div class="mt-1 text-sm font-semibold text-slate-800 dark:text-slate-100"><?php echo e($tech); ?></div>
                                    </div>
                                    <div class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">Dalam tugas</div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/><path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"/></svg>
                        </div>
                        <div class="empty-title">Belum ada jadwal piket</div>
                        <div class="empty-desc">Jadwal piket aktif saat ini belum tersedia.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Kalender Zoom -->
        <div class="card">
            <div class="card-head">
                <div>
                    <div class="card-title">Kalender Zoom</div>
                    <div class="card-sub">Zoom dengan link aktif • <?php echo e(now()->format('F Y')); ?></div>
                </div>
                <div class="legend">
                    <div class="dot" style="background:#3b82f6"></div> Approved
                </div>
            </div>
            <div class="card-body" style="padding: 12px 16px;">
                <div id="zoomCalendar" style="height:auto;"></div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="two-col">
        <div class="card">
            <div class="card-head">
                <div>
                    <div class="card-title">Grafik Tiket Permasalahan</div>
                    <div class="card-sub">Distribusi status tiket</div>
                </div>
                <div style="display:flex;align-items:center;gap:10px;">
                    <select id="monthFilter">
                        <option value="all">Semua</option>
                        <?php for($i = 1; $i <= 12; $i++): ?>
                            <option value="<?php echo e(date('Y')); ?>-<?php echo e(str_pad($i, 2, '0', STR_PAD_LEFT)); ?>" <?php echo e($i == date('n') ? 'selected' : ''); ?>>
                                <?php echo e(\Carbon\Carbon::create(date('Y'), $i, 1)->locale('id')->format('F Y')); ?>

                            </option>
                        <?php endfor; ?>
                    </select>
                    <a href="<?php echo e(route('exports.tickets')); ?>" class="link">Ekspor CSV</a>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-wrap"><canvas id="ticketChart"></canvas></div>
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <div>
                    <div class="card-title">Grafik Pengajuan Zoom</div>
                    <div class="card-sub">Distribusi status reservasi</div>
                </div>
                <a href="<?php echo e(route('exports.reservations')); ?>" class="link">Ekspor CSV</a>
            </div>
            <div class="card-body">
                <div class="chart-wrap"><canvas id="zoomChart"></canvas></div>
            </div>
        </div>
    </div>

    <!-- Latest Tickets + Zoom -->
    <div class="two-col">
        <div class="card">
            <div class="card-head">
                <div class="card-title">Tiket Terbaru</div>
                <a href="<?php echo e(route('tickets.index')); ?>" class="link">Lihat semua →</a>
            </div>
            <div class="card-body no-pad">
                <?php $__empty_1 = true; $__currentLoopData = $recentTickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="ticket-item">
                        <div class="ticket-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
                        </div>
                        <div class="ticket-meta">
                            <div class="ticket-title"><?php echo e($ticket->title); ?></div>
                            <div class="ticket-code"><?php echo e($ticket->code); ?> • <?php echo e($ticket->category_label); ?></div>
                        </div>
                        <span class="ticket-badge <?php echo e($ticket->status_badge_class); ?>"><?php echo e($ticket->status_label); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="empty-state" style="padding:20px;">
                        <div class="empty-desc">Belum ada tiket.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <div class="card-title">Pengajuan Zoom Terbaru</div>
                <a href="<?php echo e(route('reservations.index')); ?>" class="link">Lihat semua →</a>
            </div>
            <div class="card-body no-pad">
                <?php $__empty_1 = true; $__currentLoopData = $recentReservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reservation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="ticket-item">
                        <div class="ticket-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                        </div>
                        <div class="ticket-meta">
                            <div class="ticket-title"><?php echo e($reservation->room_name); ?></div>
                            <div class="ticket-code"><?php echo e(optional($reservation->start_time)->format('d/m/Y H:i')); ?> • <?php echo e(\Illuminate\Support\Str::limit($reservation->purpose, 40)); ?></div>
                        </div>
                        <span class="ticket-badge <?php echo e($reservation->status_badge_class); ?>"><?php echo e($reservation->status_label); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="empty-state" style="padding:36px 20px;">
                        <div class="empty-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                        </div>
                        <div class="empty-title">Belum ada pengajuan Zoom</div>
                        <div class="empty-desc">Pengajuan Zoom akan muncul di sini.</div>
                    </div>
                <?php endif; ?>
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

    /* Biarkan FullCalendar mengatur tinggi agar seluruh bulan terlihat */
    #zoomCalendar {
        height: auto;
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
                events: <?php echo json_encode($calendarEventsArray ?? $zoomEventsArray, 15, 512) ?>,
                height: 'auto',
                contentHeight: 'auto',
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
                    const props = info.event.extendedProps || {};
                    let tooltip = '';
                    if (props.technicians) {
                        tooltip = `
                            <div class="bg-gray-900 text-white p-3 rounded-lg shadow-lg max-w-[18rem]">
                                <div class="font-semibold mb-2">Piket • ${props.week_start} - ${props.week_end}</div>
                                <div class="text-xs sm:text-sm space-y-1">
                                    <div><strong>Tim:</strong> ${props.technicians.join(', ')}</div>
                                    <div><strong>Aktif:</strong> ${props.is_active ? 'Ya' : 'Tidak'}</div>
                                </div>
                            </div>
                        `;
                    } else {
                        tooltip = `
                            <div class="bg-gray-900 text-white p-3 rounded-lg shadow-lg max-w-[18rem]">
                                <div class="font-semibold mb-2">${props.room || info.event.title}</div>
                                <div class="text-xs sm:text-sm space-y-1">
                                    <div><strong>Pemohon:</strong> ${props.requester || 'Tidak diketahui'}</div>
                                    <div><strong>Detail:</strong> ${props.purpose || ''}</div>
                                    <div><strong>Status:</strong> ${props.status || ''}</div>
                                    <div><strong>Mulai:</strong> ${props.start || ''}</div>
                                    <div><strong>Selesai:</strong> ${props.end || ''}</div>
                                </div>
                            </div>
                        `;
                    }

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