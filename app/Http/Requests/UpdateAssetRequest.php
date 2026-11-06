<?php

namespace App\Http\Requests;

use App\Models\Asset;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        if ($user->hasAnyRole(['Admin', 'Teknisi'])) {
            return true;
        }

        // Pemegang aset boleh mengedit asetnya sendiri (dibatasi hanya foto di controller).
        $asset = $this->route('asset');
        if (! $asset) {
            return false;
        }

        return $asset->user_id === $user->id
            || mb_strtolower(trim((string) $asset->holder)) === mb_strtolower(trim((string) $user->name));
    }

    public function rules(): array
    {
        return [
            'asset_code' => 'sometimes|string|max:50|unique:assets,asset_code,'.$this->asset->id,
            'name' => 'sometimes|string|max:150',
            'type' => 'sometimes|string|max:50',
            'brand' => 'nullable|string|max:80',
            'model' => 'nullable|string|max:80',
            'serial_number' => 'nullable|string|max:120|unique:assets,serial_number,'.$this->asset->id,
            'location' => 'nullable|string|max:120',
            'holder' => 'nullable|string|max:120',
            'nilai_perolehan' => 'nullable|numeric|min:0',
            'kode_satker' => 'nullable|string|max:50',
            'nip_pegawai' => 'nullable|string|max:30',
            'status' => ['sometimes', 'string', Rule::in(array_merge(array_keys(Asset::statusOptions()), [
                Asset::STATUS_ALLOCATED,
                Asset::STATUS_READY_TO_ALLOCATE,
                Asset::STATUS_NOT_ALLOCATABLE,
            ]))],
            'condition' => ['sometimes', 'string', Rule::in(array_merge(array_keys(Asset::conditionOptions()), [
                Asset::CONDITION_GOOD,
                Asset::CONDITION_LIGHT,
                Asset::CONDITION_HEAVY,
            ]))],
            'purchased_at' => 'nullable|date',
            'photo_serial' => 'nullable|image|max:5120',
            'photo_serial_url' => 'nullable|url',
            'photo_asset' => 'nullable|image|max:5120',
            'photo_asset_url' => 'nullable|url',
            'photo_bmn' => 'nullable|image|max:5120',
            'photo_bmn_url' => 'nullable|url',
        ];
    }
}
