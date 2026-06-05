<?php

namespace App\Http\Requests;

use App\Models\PiketSchedule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $ticket = $this->route('ticket');

        if (! $user) {
            return false;
        }

        return $user->hasAnyRole(['Admin', 'Teknisi'])
            || ($ticket && $ticket->requester_id === $user->id);
    }

    public function rules(): array
    {
        $allowedStatus = array_merge([''], \App\Models\Ticket::statuses());

        return [
            'category' => 'sometimes|string|in:DATA_PROCESSING,EMAIL_SSO,HARDWARE_SUPPORT,SOFTWARE_SUPPORT,NETWORK_SUPPORT,SECURITY_INCIDENT,OTHER',
            'title' => 'sometimes|string|max:200',
            'description' => 'sometimes|string|max:5000',
            'status' => ['nullable', 'string', Rule::in($allowedStatus)],
            'asset_id' => 'nullable|integer|exists:assets,id',
            'assignee_id' => 'nullable|integer|exists:users,id',
            'attachment' => 'nullable|file|max:1024',
        ];
    }

    public function messages(): array
    {
        return [
            'attachment.file' => 'Lampiran harus berupa file.',
            'attachment.max' => 'Ukuran lampiran maksimal 1MB.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validasi untuk assignee_id dihapus - memungkinkan penugasan dari semua teknisi
        });
    }
}
