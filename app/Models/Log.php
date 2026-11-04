<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'actor_id',
        'entity_type',
        'entity_id',
        'action',
        'meta',
        'ip_address',
        'user_agent',
        'request_method',
        'request_path',
        'created_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'created_at' => 'datetime',
    ];

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id')->withTrashed();
    }
}
