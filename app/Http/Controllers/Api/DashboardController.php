<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Ticket;
use App\Models\Reservation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function summary()
    {
        // Single query for asset counts
        $assetCounts = \DB::table('assets')
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'ACTIVE' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status = 'INACTIVE' THEN 1 ELSE 0 END) as inactive,
                SUM(CASE WHEN condition = 'HEAVY' THEN 1 ELSE 0 END) as damaged,
                SUM(CASE WHEN condition = 'LIGHT' THEN 1 ELSE 0 END) as light_damage
            ")
            ->first();

        // Single query for ticket counts
        $ticketCounts = \DB::table('tickets')
            ->selectRaw("
                SUM(CASE WHEN status = 'OPEN' THEN 1 ELSE 0 END) as open,
                SUM(CASE WHEN status = 'ASSIGNED_DETECT' THEN 1 ELSE 0 END) as assigned_detect,
                SUM(CASE WHEN status = 'SOLVED_WITH_NOTES' THEN 1 ELSE 0 END) as solved_with_notes,
                SUM(CASE WHEN status = 'SOLVED' THEN 1 ELSE 0 END) as solved,
                SUM(CASE WHEN status = 'REJECTED' THEN 1 ELSE 0 END) as rejected
            ")
            ->first();

        return response()->json([
            'assets' => [
                'total' => $assetCounts->total,
                'active' => $assetCounts->active,
                'inactive' => $assetCounts->inactive,
                'damaged' => $assetCounts->damaged,
            ],
            'tickets' => [
                'open' => $ticketCounts->open,
                'assigned_detect' => $ticketCounts->assigned_detect,
                'solved_with_notes' => $ticketCounts->solved_with_notes,
                'solved' => $ticketCounts->solved,
                'rejected' => $ticketCounts->rejected,
            ],
            'latest_tickets' => Ticket::latest()->take(10)->get([
                'id', 'code', 'title', 'status', 'priority', 'created_at',
            ]),
        ]);
    }

    public function charts(Request $request)
    {
        $user = auth()->user();
        $isAdminOrTechnician = $user->hasRole(['Admin', 'Teknisi']);
        $month = $request->get('month', 'all');

        $ticketQuery = $isAdminOrTechnician ? Ticket::query() : Ticket::where('requester_id', $user->id);
        $zoomQuery = $isAdminOrTechnician ? Reservation::query() : Reservation::where('requester_id', $user->id);

        if ($month !== 'all') {
            [$year, $monthNum] = explode('-', $month);
            $ticketQuery->whereYear('created_at', $year)->whereMonth('created_at', $monthNum);
            $zoomQuery->whereYear('created_at', $year)->whereMonth('created_at', $monthNum);
        }

        $ticketCounts = [
            'Dibuka' => (clone $ticketQuery)->where('status', Ticket::STATUS_OPEN)->count(),
            'Diproses Teknisi' => (clone $ticketQuery)->where('status', Ticket::STATUS_ASSIGNED_DETECT)->count(),
            'Menunggu Ketersediaan Barang' => (clone $ticketQuery)->where('status', Ticket::STATUS_WAITING_PARTS)->count(),
            'Selesai + Catatan' => (clone $ticketQuery)->where('status', Ticket::STATUS_SOLVED_WITH_NOTES)->count(),
            'Selesai' => (clone $ticketQuery)->where('status', Ticket::STATUS_SOLVED)->count(),
            'Batal' => (clone $ticketQuery)->whereIn('status', [Ticket::STATUS_REJECTED, Ticket::STATUS_CANCELLED])->count(),
        ];

        $zoomCounts = [
            'Dibuka' => (clone $zoomQuery)->where('status', Reservation::STATUS_PENDING)->count(),
            'Diproses Teknisi' => (clone $zoomQuery)->where('status', Reservation::STATUS_APPROVED)->count(),
            'Zoom Siap' => (clone $zoomQuery)->where('status', Reservation::STATUS_WAITING_MONITORING)->count(),
            'Selesai' => (clone $zoomQuery)->where('status', Reservation::STATUS_COMPLETED)->count(),
            'Selesai Ditolak' => (clone $zoomQuery)->where('status', Reservation::STATUS_REJECTED)->count(),
            'Batal' => (clone $zoomQuery)->where('status', Reservation::STATUS_CANCELLED)->count(),
        ];

        return response()->json([
            'ticketCounts' => $ticketCounts,
            'zoomCounts' => $zoomCounts,
        ]);
    }
}
