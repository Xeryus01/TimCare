<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketCommentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'ticket_id' => $this->ticket_id,
            'message' => $this->message,
            'is_internal' => (bool) $this->is_internal,
            'user' => new UserResource($this->whenLoaded('user')),
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
