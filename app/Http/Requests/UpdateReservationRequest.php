<?php

namespace App\Http\Requests;

use App\Models\PiketSchedule;
use App\Models\Reservation;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $reservation = $this->route('reservation');

        if (! $user) {
            return false;
        }

        return $user->hasAnyRole(['Admin', 'Teknisi'])
            || ($reservation && $reservation->requester_id === $user->id);
    }

    public function rules(): array
    {
        return [
            'room_name' => 'nullable|string|max:100',
            'team_name' => 'nullable|string|max:100',
            'purpose' => 'nullable|string|max:500',
            'participants_count' => 'nullable|integer|min:1',
            'operator_needed' => 'nullable|boolean',
            'breakroom_needed' => 'nullable|boolean',
            'start_time_local' => 'nullable|regex:/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}(:\d{2})?$/',
            'end_time_local' => 'nullable|regex:/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}(:\d{2})?$/',
            'status' => 'nullable|string|in:' . implode(',', Reservation::statuses()),
            'zoom_link' => 'nullable|url|max:255',
            'zoom_record_link' => 'nullable|url|max:255',
            'notes' => 'nullable|string|max:2000',
            'approver_id' => 'nullable|integer|exists:users,id',
            'nota_dinas' => 'nullable|file|mimes:pdf|max:1024',
        ];
    }

    public function messages(): array
    {
        return [
            'nota_dinas.file' => 'Nota dinas harus berupa file.',
            'nota_dinas.mimes' => 'Nota dinas harus berformat PDF.',
            'nota_dinas.max' => 'Ukuran nota dinas maksimal 1MB.',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->filled('start_time_local')) {
            // Convert datetime-local format (2026-04-08T14:30) to database format (2026-04-08 14:30:00)
            $startTime = $this->input('start_time_local');
            if (strlen($startTime) == 16) { // Format: YYYY-MM-DDTHH:MM
                $startTime .= ':00'; // Add seconds
            }
            $this->merge([
                'start_time' => $startTime
            ]);
        }

        if ($this->filled('end_time_local')) {
            // Convert datetime-local format (2026-04-08T14:30) to database format (2026-04-08 14:30:00)
            $endTime = $this->input('end_time_local');
            if (strlen($endTime) == 16) { // Format: YYYY-MM-DDTHH:MM
                $endTime .= ':00'; // Add seconds
            }
            $this->merge([
                'end_time' => $endTime
            ]);
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Check if times are provided and end time is after start time
            if ($this->filled('start_time_local') && $this->filled('end_time_local')) {
                $startTime = $this->input('start_time');
                $endTime = $this->input('end_time');

                if (strtotime($endTime) <= strtotime($startTime)) {
                    $validator->errors()->add('end_time_local', 'Waktu selesai harus setelah waktu mulai.');
                }
            }

            // For admins, zoom_link is required when changing status to APPROVED
            $user = $this->user();
            if ($user && $user->hasPermissionTo('approve reservations')) {
                if ($this->filled('status') && $this->input('status') === 'APPROVED' && ! $this->filled('zoom_link')) {
                    $validator->errors()->add('zoom_link', 'Link Zoom harus diisi ketika menyetujui pengajuan.');
                }
            }

            if ($this->filled('approver_id') && $user && ! $user->hasRole('Admin')) {
                $validator->errors()->add('approver_id', 'Hanya Admin yang dapat menugaskan petugas.');
            }

            // Validasi untuk approver_id dihapus - memungkinkan penugasan dari semua teknisi

            // If status is being changed to REJECTED, CANCELLED, or COMPLETED, zoom_link is not required
            if ($this->filled('status')) {
                $status = $this->input('status');
                if (in_array($status, ['REJECTED', 'CANCELLED', 'COMPLETED']) && !$this->filled('zoom_link')) {
                    // Allow empty zoom_link for these statuses - no error needed
                }
            }
        });
    }
}
