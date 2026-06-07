<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Reservation extends Model
{
    public const STATUS_PENDING = 'PENDING';
    public const STATUS_APPROVED = 'APPROVED';
    public const STATUS_WAITING_MONITORING = 'WAITING_MONITORING';
    public const STATUS_COMPLETED = 'COMPLETED';
    public const STATUS_REJECTED = 'REJECTED';
    public const STATUS_CANCELLED = 'CANCELLED';

    protected $fillable = [
        'code',
        'requester_id',
        'room_name',
        'purpose',
        'team_name',
        'operator_needed',
        'breakroom_needed',
        'participants_count',
        'start_time',
        'end_time',
        'status',
        'approver_id',
        'notes',
        'zoom_link',
        'zoom_record_link',
        'nota_dinas_path',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'operator_needed' => 'boolean',
        'breakroom_needed' => 'boolean',
        'participants_count' => 'integer',
    ];

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING => 'Dibuka',
            self::STATUS_APPROVED => 'Diproses Teknisi',
            self::STATUS_WAITING_MONITORING => 'Zoom Siap',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_REJECTED => 'Selesai Ditolak',
            self::STATUS_CANCELLED => 'Batal',
        ];
    }

    public static function statusBadgeClasses(): array
    {
        return [
            self::STATUS_PENDING => 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400',
            self::STATUS_APPROVED => 'bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-400',
            self::STATUS_WAITING_MONITORING => 'bg-purple-100 text-purple-700 dark:bg-purple-500/15 dark:text-purple-400',
            self::STATUS_COMPLETED => 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-400',
            self::STATUS_REJECTED => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400',
            self::STATUS_CANCELLED => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400',
        ];
    }

    public static function statusBadgeClassNames(): array
    {
        return [
            self::STATUS_PENDING => 'open',
            self::STATUS_APPROVED => 'assigned',
            self::STATUS_WAITING_MONITORING => 'waiting',
            self::STATUS_COMPLETED => 'done',
            self::STATUS_REJECTED => 'cancelled',
            self::STATUS_CANCELLED => 'cancelled',
        ];
    }

    public function getStatusBadgeClassesAttribute(): string
    {
        return self::statusBadgeClasses()[$this->status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-500/15 dark:text-gray-400';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return self::statusBadgeClassNames()[$this->status] ?? 'cancelled';
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_APPROVED,
            self::STATUS_WAITING_MONITORING,
            self::STATUS_COMPLETED,
            self::STATUS_REJECTED,
            self::STATUS_CANCELLED,
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function logs()
    {
        return $this->hasMany(Log::class, 'entity_id')->where('entity_type', 'Reservation');
    }

    public static function generateCode(): string
    {
        $prefix = 'RES';
        $date = now()->format('Ymd');

        DB::beginTransaction();

        try {
            $sequence = DB::table('code_sequences')
                ->where('date', $date)
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                DB::table('code_sequences')->insert([
                    'date' => $date,
                    'ticket_count' => 0,
                    'reservation_count' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $nextSequence = 1;
            } else {
                $nextSequence = $sequence->reservation_count + 1;
                DB::table('code_sequences')
                    ->where('date', $date)
                    ->update(['reservation_count' => $nextSequence, 'updated_at' => now()]);
            }

            DB::commit();

            return sprintf('%s-%s-%05d', $prefix, $date, $nextSequence);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
