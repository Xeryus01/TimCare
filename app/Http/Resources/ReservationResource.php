<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'room_name' => $this->room_name,
            'team_name' => $this->team_name,
            'purpose' => $this->purpose,
            'participants_count' => $this->participants_count,
            'operator_needed' => (bool) $this->operator_needed,
            'breakroom_needed' => (bool) $this->breakroom_needed,
            'start_time' => optional($this->start_time)->toISOString(),
            'end_time' => optional($this->end_time)->toISOString(),
            'status' => $this->status,
            'status_label' => $this->status_label,
            'status_badge_class' => $this->status_badge_class,
            'notes' => $this->notes,
            'zoom_link' => $this->zoom_link,
            'zoom_record_link' => $this->zoom_record_link,
            'nota_dinas_path' => $this->nota_dinas_path,
            'requester' => new UserResource($this->whenLoaded('requester')),
            'approver' => new UserResource($this->whenLoaded('approver')),
            'created_at' => optional($this->created_at)->toISOString(),
            'service_rating' => $this->service_rating,
            'service_speed_rating' => $this->service_speed_rating,
            'service_quality_rating' => $this->service_quality_rating,
            'service_communication_rating' => $this->service_communication_rating,
            'service_feedback' => $this->service_feedback,
            'rated_at' => optional($this->rated_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
