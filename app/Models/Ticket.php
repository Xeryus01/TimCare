<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Ticket extends Model
{
    use HasFactory;
    // status constants for improved consistency
    public const STATUS_OPEN = 'Dibuka';
    public const STATUS_ASSIGNED_DETECT = 'Diproses Teknisi';
    public const STATUS_WAITING_PARTS = 'Menunggu Ketersediaan Barang';
    public const STATUS_SOLVED_WITH_NOTES = 'Selesai dengan Catatan';
    public const STATUS_SOLVED = 'Selesai';
    public const STATUS_REJECTED = 'Ditolak';
    public const STATUS_CANCELLED = 'Dibatalkan';

    /**
     * All valid ticket statuses, used for validation and dropdowns.
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_OPEN,
            self::STATUS_ASSIGNED_DETECT,
            self::STATUS_WAITING_PARTS,
            self::STATUS_SOLVED_WITH_NOTES,
            self::STATUS_SOLVED,
            self::STATUS_REJECTED,
            self::STATUS_CANCELLED,
        ];
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_OPEN => 'Dibuka',
            self::STATUS_ASSIGNED_DETECT => 'Diproses Teknisi',
            self::STATUS_WAITING_PARTS => 'Menunggu Ketersediaan Barang',
            self::STATUS_SOLVED_WITH_NOTES => 'Selesai dengan Catatan',
            self::STATUS_SOLVED => 'Selesai',
            self::STATUS_REJECTED => 'Ditolak',
            self::STATUS_CANCELLED => 'Dibatalkan',
        ];
    }

    public static function priorityLabels(): array
    {
        return [
            'LOW' => 'Rendah',
            'MEDIUM' => 'Sedang',
            'HIGH' => 'Tinggi',
            'CRITICAL' => 'Darurat',
        ];
    }

    public static function categoryLabels(): array
    {
        return [
            'DATA_PROCESSING' => 'Pengolahan Data',
            'EMAIL_SSO' => 'Layanan Email dan SSO BPS',
            'HARDWARE_SUPPORT' => 'Layanan Perangkat Keras',
            'SOFTWARE_SUPPORT' => 'Layanan Perangkat Lunak',
            'NETWORK_SUPPORT' => 'Layanan Jaringan',
            'SECURITY_INCIDENT' => 'Insiden Keamanan',
            'OTHER' => 'Lainnya',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    public function getPriorityLabelAttribute(): string
    {
        return self::priorityLabels()[$this->priority] ?? $this->priority;
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::categoryLabels()[$this->category] ?? $this->category;
    }

    protected $fillable = [
        'code',
        'requester_id',
        'assignee_id',
        'asset_id',
        'category',
        'title',
        'description',
        'priority',
        'status',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function logs()
    {
        return $this->hasMany(Log::class, 'entity_id')->where('entity_type', 'Ticket');
    }

    public static function generateCode(): string
    {
        $prefix = 'TKT';
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
                    'ticket_count' => 1,
                    'reservation_count' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $nextSequence = 1;
            } else {
                $nextSequence = $sequence->ticket_count + 1;
                DB::table('code_sequences')
                    ->where('date', $date)
                    ->update(['ticket_count' => $nextSequence, 'updated_at' => now()]);
            }

            DB::commit();

            return sprintf('%s-%s-%05d', $prefix, $date, $nextSequence);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
