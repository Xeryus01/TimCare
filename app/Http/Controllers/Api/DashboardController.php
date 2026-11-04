<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Ticket;
use App\Models\Reservation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function summary(Request $request)
    {
        $user = $request->user();
        $isStaff = $user->hasAnyRole(['Admin', 'Teknisi']);

        $assetQuery = Asset::query();
        if (! $isStaff) {
            $assetQuery->where(function ($query) use ($user) {
                $query->where('user_id', $user->id);
                $name = trim((string) $user->name);
                if ($name !== '') {
                    $query->orWhereRaw('LOWER(holder) = ?', [mb_strtolower($name)]);
                }
            });
        }

        $ticketQuery = $isStaff
            ? Ticket::query()
            : Ticket::where('requester_id', $user->id);

        return response()->json([
            'assets' => [
                'total' => (clone $assetQuery)->count(),
                'active' => (clone $assetQuery)->where('status', 'ACTIVE')->count(),
                'inactive' => (clone $assetQuery)->where('status', 'INACTIVE')->count(),
                'damaged' => (clone $assetQuery)->where('condition', 'HEAVY')->count(),
            ],
            'tickets' => [
                'open' => (clone $ticketQuery)->where('status', Ticket::STATUS_OPEN)->count(),
                'assigned_detect' => (clone $ticketQuery)->where('status', Ticket::STATUS_ASSIGNED_DETECT)->count(),
                'solved_with_notes' => (clone $ticketQuery)->where('status', Ticket::STATUS_SOLVED_WITH_NOTES)->count(),
                'solved' => (clone $ticketQuery)->where('status', Ticket::STATUS_SOLVED)->count(),
                'rejected' => (clone $ticketQuery)->where('status', Ticket::STATUS_REJECTED)->count(),
            ],
            'latest_tickets' => (clone $ticketQuery)->latest()->take(10)->get([
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
