<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'ticket_id' => $this->ticket_id,
            'comment_id' => $this->comment_id,
            'file_name' => $this->file_name,
            'mime_type' => $this->mime_type,
            'size_bytes' => $this->size_bytes,
            'url' => $this->file_path
                ? route('tickets.attachments.show', [
                    'ticket' => $this->ticket_id,
                    'attachment' => $this->id,
                ])
                : null,
            'uploader' => new UserResource($this->whenLoaded('uploader')),
            'created_at' => optional($this->created_at)->toISOString(),
        ];
    }
}
