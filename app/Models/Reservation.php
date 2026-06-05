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
