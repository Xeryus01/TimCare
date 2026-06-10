<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => 'required|in:DATA_PROCESSING,EMAIL_SSO,HARDWARE_SUPPORT,SOFTWARE_SUPPORT,NETWORK_SUPPORT,SECURITY_INCIDENT,OTHER',
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'priority' => 'nullable|in:LOW,MEDIUM,HIGH,CRITICAL',
            'asset_id' => 'nullable|exists:assets,id',
            'attachment' => 'required|file|max:1024',
        ];
    }

    public function messages(): array
    {
        return [
            'attachment.required' => 'Lampiran awal tiket harus diunggah.',
            'attachment.file' => 'Lampiran harus berupa file.',
            'attachment.max' => 'Ukuran lampiran maksimal 1MB.',
        ];
    }
}
