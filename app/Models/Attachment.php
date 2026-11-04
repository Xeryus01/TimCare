<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = [
        'ticket_id',
        'comment_id',
        'uploader_id',
        'file_path',
        'file_name',
        'mime_type',
        'size_bytes',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function comment()
    {
        return $this->belongsTo(TicketComment::class, 'comment_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploader_id')->withTrashed();
    }
}
