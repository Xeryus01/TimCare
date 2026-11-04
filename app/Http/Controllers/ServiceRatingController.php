<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Ticket;
use App\Services\AuditTrailService;
use Illuminate\Http\Request;

class ServiceRatingController extends Controller
{
    public function __construct(private AuditTrailService $auditTrailService) {}

    public function rateTicket(Request $request, Ticket $ticket)
    {
        if ($ticket->requester_id !== $request->user()->id) {
            abort(403, 'Hanya pemohon tiket yang dapat memberi penilaian.');
        }

        if (! in_array($ticket->status, [Ticket::STATUS_SOLVED, Ticket::STATUS_SOLVED_WITH_NOTES], true)) {
            abort(403, 'Penilaian hanya dapat diberikan setelah tiket selesai.');
        }

        $data = $request->validate([
            'service_speed_rating' => 'required|integer|min:1|max:5',
            'service_quality_rating' => 'required|integer|min:1|max:5',
            'service_communication_rating' => 'required|integer|min:1|max:5',
            'service_feedback' => 'nullable|string|max:1000',
        ]);

        $before = $ticket->only(['service_rating', 'service_speed_rating', 'service_quality_rating', 'service_communication_rating', 'service_feedback', 'rated_at']);
        $averageRating = round(($data['service_speed_rating'] + $data['service_quality_rating'] + $data['service_communication_rating']) / 3);
        $ticket->update([
            'service_rating' => $averageRating,
            'service_speed_rating' => $data['service_speed_rating'],
            'service_quality_rating' => $data['service_quality_rating'],
            'service_communication_rating' => $data['service_communication_rating'],
            'service_feedback' => $data['service_feedback'] ?? null,
            'rated_at' => now(),
        ]);

        $this->auditTrailService->record($request, $request->user()->id, 'TICKET', $ticket->id, 'SERVICE_RATED', [
            'before' => $before,
            'after' => $ticket->only(['service_rating', 'service_speed_rating', 'service_quality_rating', 'service_communication_rating', 'service_feedback', 'rated_at']),
        ]);

        return back()->with('success', 'Terima kasih, penilaian layanan tiket berhasil disimpan.');
    }

    public function rateReservation(Request $request, Reservation $reservation)
    {
        if ($reservation->requester_id !== $request->user()->id) {
            abort(403, 'Hanya pemohon pengajuan Zoom yang dapat memberi penilaian.');
        }

        if ($reservation->status !== Reservation::STATUS_COMPLETED) {
            abort(403, 'Penilaian hanya dapat diberikan setelah pengajuan Zoom selesai.');
        }

        $data = $request->validate([
            'service_speed_rating' => 'required|integer|min:1|max:5',
            'service_quality_rating' => 'required|integer|min:1|max:5',
            'service_communication_rating' => 'required|integer|min:1|max:5',
            'service_feedback' => 'nullable|string|max:1000',
        ]);

        $before = $reservation->only(['service_rating', 'service_speed_rating', 'service_quality_rating', 'service_communication_rating', 'service_feedback', 'rated_at']);
        $averageRating = round(($data['service_speed_rating'] + $data['service_quality_rating'] + $data['service_communication_rating']) / 3);
        $reservation->update([
            'service_rating' => $averageRating,
            'service_speed_rating' => $data['service_speed_rating'],
            'service_quality_rating' => $data['service_quality_rating'],
            'service_communication_rating' => $data['service_communication_rating'],
            'service_feedback' => $data['service_feedback'] ?? null,
            'rated_at' => now(),
        ]);

        $this->auditTrailService->record($request, $request->user()->id, 'Reservation', $reservation->id, 'SERVICE_RATED', [
            'before' => $before,
            'after' => $reservation->only(['service_rating', 'service_speed_rating', 'service_quality_rating', 'service_communication_rating', 'service_feedback', 'rated_at']),
        ]);

        return back()->with('success', 'Terima kasih, penilaian layanan Zoom berhasil disimpan.');
    }
}
