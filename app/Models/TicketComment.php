<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketComment extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'is_internal',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'comment_id');
    }
}
