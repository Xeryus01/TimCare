<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['Admin', 'Teknisi']) ?? false;
    }

    public function rules(): array
    {
        return [
            'asset_code' => 'required|string|max:50|unique:assets',
            'name' => 'required|string|max:150',
            'type' => 'required|string|max:50',
            'brand' => 'nullable|string|max:80',
            'model' => 'nullable|string|max:80',
            'serial_number' => 'nullable|string|max:120|unique:assets',
            'specs' => 'nullable|json',
            'location' => 'nullable|string|max:120',
            'holder' => 'nullable|string|max:120',
            'nilai_perolehan' => 'nullable|numeric|min:0',
            'kode_satker' => 'nullable|string|max:50',
            'nip_pegawai' => 'nullable|string|max:30',
            'status' => 'required|string|in:ACTIVE,INACTIVE,PENDING',
            'condition' => 'required|string|in:GOOD,LIGHT,HEAVY',
            'purchased_at' => 'nullable|date',
        ];
    }
}
