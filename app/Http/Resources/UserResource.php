<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->when($request->user()?->hasRole('Admin'), $this->email),
            'phone_number' => $this->when($request->user()?->hasRole('Admin'), $this->phone_number ?? null),
        ];
    }
}
