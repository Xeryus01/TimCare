<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'category' => $this->category,
            'category_label' => $this->category_label,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'priority_label' => $this->priority_label,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'status_badge_class' => $this->status_badge_class,
            'requester' => new UserResource($this->whenLoaded('requester')),
            'assignee' => new UserResource($this->whenLoaded('assignee')),
            'asset' => new AssetResource($this->whenLoaded('asset')),
            'comments' => TicketCommentResource::collection($this->whenLoaded('comments')),
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'resolved_at' => optional($this->resolved_at)->toISOString(),
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
