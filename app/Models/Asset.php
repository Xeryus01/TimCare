<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    public const STATUS_ALLOCATED = 'ACTIVE';
    public const STATUS_NOT_ALLOCATABLE = 'INACTIVE';
    public const STATUS_READY_TO_ALLOCATE = 'PENDING';

    public const CONDITION_GOOD = 'GOOD';
    public const CONDITION_LIGHT = 'LIGHT';
    public const CONDITION_HEAVY = 'HEAVY';

    protected $fillable = [
        'asset_code',
        'name',
        'type',
        'brand',
        'model',
        'serial_number',
        'photo_serial',
        'photo_asset',
        'photo_bmn',
        'specs',
        'location',
        'holder',
        'status',
        'condition',
        'purchased_at',
        'nilai_perolehan',
        'kode_satker',
        'nip_pegawai',
        'user_id',
    ];

    protected $casts = [
        'specs' => 'array',
        'purchased_at' => 'date',
    ];

    public static function extractGoogleDriveId(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        if (str_starts_with($value, 'drive:')) {
            return substr($value, 6);
        }

        $patterns = [
            '/drive\.google\.com\/file\/d\/([^\/\?]+)/',
            '/drive\.google\.com\/open\?id=([^&]+)/',
            '/drive\.google\.com\/uc\?id=([^&]+)/',
            '/drive\.google\.com\/drive\/folders\/([^\/\?]+)/',
            '/^([a-zA-Z0-9_-]{10,})$/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $value, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    public static function googleDrivePreviewUrl(string $fileId): string
    {
        return "https://drive.google.com/file/d/{$fileId}/preview";
    }

    public static function googleDriveFileLink(string $fileId): string
    {
        return "https://drive.google.com/file/d/{$fileId}/view";
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_ALLOCATED => 'Teralokasi',
            self::STATUS_READY_TO_ALLOCATE => 'Siap Dialokasikan',
            self::STATUS_NOT_ALLOCATABLE => 'Tidak Dapat Dialokasikan',
        ];
    }

    public static function conditionOptions(): array
    {
        return [
            self::CONDITION_GOOD => 'Baik',
            self::CONDITION_LIGHT => 'Rusak Ringan',
            self::CONDITION_HEAVY => 'Rusak Berat',
        ];
    }

    public static function normalizeCondition(?string $condition): string
    {
        if ($condition === null) {
            return self::CONDITION_GOOD;
        }

        $value = strtoupper(trim((string) $condition));

        return match ($value) {
            'GOOD', 'BAIK' => self::CONDITION_GOOD,
            'FAIR', 'CUKUP', 'LIGHT', 'LIGHT_DAMAGE', 'RUSAK_RINGAN', 'RUSAK RINGAN', 'RINGAN' => self::CONDITION_LIGHT,
            'POOR', 'BURUK', 'DAMAGED', 'RUSAK', 'RUSAK_BERAT', 'RUSAK BERAT', 'HEAVY', 'HEAVY_DAMAGE', 'BERAT' => self::CONDITION_HEAVY,
            default => self::CONDITION_GOOD,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ALLOCATED => 'Teralokasi',
            self::STATUS_READY_TO_ALLOCATE => 'Siap Dialokasikan',
            self::STATUS_NOT_ALLOCATABLE => 'Tidak Dapat Dialokasikan',
            'DECOMMISSIONED', 'MAINTENANCE', 'BROKEN', 'RETIRED' => 'Tidak Dapat Dialokasikan',
            default => ucfirst(strtolower($this->status)),
        };
    }

    public function getConditionLabelAttribute(): string
    {
        return self::conditionOptions()[$this->condition] ?? ucfirst(strtolower($this->condition));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Cari id user yang namanya cocok (case-insensitive) dengan nama pemegang.
     * Mengembalikan null jika tidak ada user dengan nama tersebut.
     */
    public static function resolveUserIdByName(?string $name): ?int
    {
        $name = trim((string) $name);
        if ($name === '') {
            return null;
        }

        return User::whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->value('id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function logs()
    {
        return $this->hasMany(Log::class, 'entity_id')->where('entity_type', 'Asset');
    }

    public function maintenanceLogs()
    {
        return $this->logs();
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function holderHistory()
    {
        return $this->hasMany(AssetHolderHistory::class)->orderBy('changed_at', 'desc');
    }

    public function maintenances()
    {
        return $this->hasMany(AssetMaintenance::class)->orderBy('maintenance_date', 'desc');
    }

    protected static function booted()
    {
        // Otomatis tautkan pemegang aset ke akun user berdasarkan namanya.
        static::saving(function (Asset $asset) {
            if ($asset->isDirty('holder') || (empty($asset->user_id) && !empty($asset->holder))) {
                $asset->user_id = self::resolveUserIdByName($asset->holder);
            }
        });

        static::updated(function () {
            \Cache::forget('assets.all');
        });

        static::deleted(function () {
            \Cache::forget('assets.all');
        });

        static::created(function () {
            \Cache::forget('assets.all');
        });
    }
}
