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
    public const STATUS_CANCELLED = 'Batal';

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
            self::STATUS_CANCELLED => 'Batal',
        ];
    }

    public static function statusBadgeClasses(): array
    {
        return [
            self::STATUS_OPEN => 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400',
            self::STATUS_ASSIGNED_DETECT => 'bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-400',
            self::STATUS_WAITING_PARTS => 'bg-purple-100 text-purple-700 dark:bg-purple-500/15 dark:text-purple-400',
            self::STATUS_SOLVED_WITH_NOTES => 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-400',
            self::STATUS_SOLVED => 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-400',
            self::STATUS_REJECTED => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400',
            self::STATUS_CANCELLED => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400',
        ];
    }

    public static function statusBadgeClassNames(): array
    {
        return [
            self::STATUS_OPEN => 'open',
            self::STATUS_ASSIGNED_DETECT => 'assigned',
            self::STATUS_WAITING_PARTS => 'waiting',
            self::STATUS_SOLVED_WITH_NOTES => 'done',
            self::STATUS_SOLVED => 'done',
            self::STATUS_REJECTED => 'cancelled',
            self::STATUS_CANCELLED => 'cancelled',
        ];
    }

    public static function normalizeStatus(string $status): string
    {
        return $status === 'Dibatalkan' ? self::STATUS_CANCELLED : $status;
    }

    public static function resolveStatusFilter(string $status): array
    {
        if ($status === self::STATUS_CANCELLED || $status === 'Dibatalkan') {
            return [self::STATUS_CANCELLED, 'Dibatalkan'];
        }

        return [$status];
    }

    public static function statusProgressSteps(): array
    {
        return [
            self::STATUS_OPEN => 'Dibuka',
            self::STATUS_ASSIGNED_DETECT => 'Diproses Teknisi',
            self::STATUS_WAITING_PARTS => 'Menunggu Ketersediaan Barang',
            self::STATUS_SOLVED_WITH_NOTES => 'Selesai dengan Catatan',
            self::STATUS_SOLVED => 'Selesai',
        ];
    }

    public static function statusProgressIndex(string $status): int
    {
        $keys = array_keys(self::statusProgressSteps());
        $index = array_search(self::normalizeStatus($status), $keys, true);

        return $index === false ? -1 : $index;
    }

    public function getStatusBadgeClassesAttribute(): string
    {
        return self::statusBadgeClasses()[self::normalizeStatus($this->status)] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-500/15 dark:text-gray-400';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return self::statusBadgeClassNames()[self::normalizeStatus($this->status)] ?? 'cancelled';
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
        return self::statusLabels()[self::normalizeStatus($this->status)] ?? $this->status;
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
        return $this->belongsTo(User::class, 'requester_id')->withTrashed();
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id')->withTrashed();
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
