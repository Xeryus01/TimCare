<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();

        $startRule = 'required|regex:/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}(:\d{2})?$/';

        // Only enforce future dates for non-admin and non-teknisi users
        if (! $user || ! $user->hasAnyRole(['Admin', 'Teknisi'])) {
            $startRule .= '|after:now';
        }

        return [
            'room_name' => 'required|string|max:100',
            'team_name' => 'nullable|string|max:100',
            'purpose' => 'required|string|max:500',
            'participants_count' => 'nullable|integer|min:1',
            'operator_needed' => 'nullable|boolean',
            'breakroom_needed' => 'nullable|boolean',
            'start_time_local' => $startRule,
            'end_time_local' => 'required|regex:/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}(:\d{2})?$/',
            'nota_dinas' => 'required|file|mimes:pdf|max:5120', // 5MB max
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

    public function messages(): array
    {
        return [
            'room_name.required' => 'Nama ruang harus diisi',
            'purpose.required' => 'Tujuan reservasi harus diisi',
            'start_time.required' => 'Waktu mulai harus diisi',
            'start_time_local.required' => 'Waktu mulai harus diisi',
            'start_time.after' => 'Waktu mulai harus di masa depan',
            'start_time_local.after' => 'Waktu mulai harus di masa depan',
            'start_time_local.regex' => 'Format waktu mulai tidak valid',
            'end_time.after' => 'Waktu selesai harus setelah waktu mulai',
            'end_time_local.regex' => 'Format waktu selesai tidak valid',
            'nota_dinas.required' => 'Nota dinas dalam format PDF harus diunggah',
            'nota_dinas.file' => 'Nota dinas harus berupa file',
            'nota_dinas.mimes' => 'Nota dinas harus berformat PDF',
            'nota_dinas.max' => 'Ukuran nota dinas maksimal 5MB',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->filled('start_time_local') && $this->filled('end_time_local')) {
                $startTime = $this->input('start_time');
                $endTime = $this->input('end_time');

                if (strtotime($endTime) <= strtotime($startTime)) {
                    $validator->errors()->add('end_time_local', 'Waktu selesai harus setelah waktu mulai.');
                } else {
                    $overlappingCount = \App\Models\Reservation::whereIn('status', [
                        \App\Models\Reservation::STATUS_PENDING,
                        \App\Models\Reservation::STATUS_APPROVED,
                        \App\Models\Reservation::STATUS_WAITING_MONITORING,
                    ])
                    ->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime)
                    ->count();

                    if ($overlappingCount >= 4) {
                        $validator->errors()->add('start_time_local', 'Slot Zoom untuk waktu tersebut sudah penuh. Silakan pilih waktu lain.');
                    }
                }
            }
        });
    }
}
