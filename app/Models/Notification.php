<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'action_type',
        'action_id',
        'is_read',
        'whatsapp_sent',
        'whatsapp_status',
        'whatsapp_response',
        'email_sent',
        'email_status',
        'email_response',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'whatsapp_sent' => 'boolean',
        'email_sent' => 'boolean',
    ];

    /**
     * Get the user that owns the notification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): void
    {
        $this->update(['is_read' => true]);
    }

    /**
     * Get unread notifications count
     */
    public static function unreadCount(User $user): int
    {
        return static::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
    }
}
