<?php

namespace App\Http\Requests;

use App\Models\Asset;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAssetMaintenanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('manage assets');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|in:PREVENTIVE,CORRECTIVE,INSPECTION',
            'maintenance_date' => 'required|date',
            'description' => 'required|string|max:1000',
            'findings' => 'nullable|string|max:2000',
            'actions_taken' => 'nullable|string|max:2000',
            'condition_before' => ['nullable', 'string', Rule::in(array_merge(array_keys(Asset::conditionOptions()), [
                Asset::CONDITION_GOOD,
                Asset::CONDITION_LIGHT,
                Asset::CONDITION_HEAVY,
            ]))],
            'condition_after' => ['nullable', 'string', Rule::in(array_merge(array_keys(Asset::conditionOptions()), [
                Asset::CONDITION_GOOD,
                Asset::CONDITION_LIGHT,
                Asset::CONDITION_HEAVY,
            ]))],
            'next_maintenance_date' => 'nullable|date|after:maintenance_date',
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Tipe perawatan harus dipilih',
            'type.in' => 'Tipe perawatan tidak valid',
            'maintenance_date.required' => 'Tanggal perawatan harus diisi',
            'maintenance_date.date' => 'Tanggal perawatan harus format yang valid',
            'description.required' => 'Deskripsi perawatan harus diisi',
            'description.string' => 'Deskripsi harus berupa teks',
            'findings.string' => 'Temuan harus berupa teks',
            'actions_taken.string' => 'Aksi yang diambil harus berupa teks',
            'next_maintenance_date.date' => 'Tanggal perawatan berikutnya harus format yang valid',
            'next_maintenance_date.after' => 'Tanggal perawatan berikutnya harus setelah tanggal perawatan',
        ];
    }
}
