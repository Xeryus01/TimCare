<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function ticketsAsRequester()
    {
        return $this->hasMany(Ticket::class, 'requester_id');
    }

    public function ticketsAsAssignee()
    {
        return $this->hasMany(Ticket::class, 'assignee_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'requester_id');
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    public function logs()
    {
        return $this->hasMany(Log::class, 'actor_id');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
