<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
{
    public function toArray($request): array
    {
        $isPrivileged = $request->user()?->hasAnyRole(['Admin', 'Teknisi']) ?? false;

        return [
            'id' => $this->id,
            'asset_code' => $this->asset_code,
            'name' => $this->name,
            'type' => $this->type,
            'brand' => $this->brand,
            'model' => $this->model,
            'serial_number' => $this->serial_number,
            'location' => $this->location,
            'holder' => $this->holder,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'condition' => $this->condition,
            'condition_label' => $this->condition_label,
            'purchased_at' => optional($this->purchased_at)->toDateString(),
            'nilai_perolehan' => $this->when($isPrivileged, $this->nilai_perolehan),
            'kode_satker' => $this->when($isPrivileged, $this->kode_satker),
            'nip_pegawai' => $this->when($isPrivileged, $this->nip_pegawai),
            'photo_serial' => $this->photo_serial,
            'photo_asset' => $this->photo_asset,
            'photo_bmn' => $this->photo_bmn,
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
