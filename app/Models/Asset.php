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
        if (! $value) {
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
            'Teralokasi' => 'Teralokasi',
            'Tidak Dapat Dialokasikan' => 'Tidak Dapat Dialokasikan',
            'Pengajuan Alokasi' => 'Pengajuan Alokasi',
            'Penetapan Pengembalian' => 'Penetapan Pengembalian',
            'Pengajuan Pengembalian' => 'Pengajuan Pengembalian',
            'Siap Dialokasikan' => 'Siap Dialokasikan',
        ];
    }

    public static function statusFilterOptions(): array
    {
        return [
            'allocated' => 'Teralokasi',
            'in_process' => 'Proses alokasi/pengembalian',
            'unavailable' => 'Tidak Dapat Dialokasikan',
        ];
    }

    public static function assetTypeGroups(): array
    {
        return [
            'computer' => [
                'CPU',
                'Lap Top',
                'Note Book',
                'P.C Unit',
                'PC Workstation',
                'Server',
                'Ultra Mobile P.C.',
                'Tablet PC',
            ],
            'network' => [
                'Auto Switch/Data Switch',
                'Firewall',
                'Mobile Modem GSM/ CDMA',
                'Modem',
                'Network Cable Tester',
                'Peralatan Jaringan Lainnya',
                'Router',
                'Switch',
                'Switch Rak',
                'Wireless Access Point',
            ],
            'peripheral' => [
                'Monitor',
                'Printer (Peralatan Personal Komputer)',
                'Scanner (Peralatan Personal Komputer)',
                'Viewer (Peralatan Personal Komputer)',
            ],
            'storage' => [
                'External',
                'External/ Portable Hardisk',
                'Hard Disk',
                'Rak Server',
                'Storage Modul Disk (Peralatan Mainframe)',
            ],
        ];
    }

    public static function assetTypeGroupOptions(): array
    {
        return [
            'computer' => 'Komputer & perangkat kerja',
            'network' => 'Jaringan',
            'peripheral' => 'Monitor & perangkat output',
            'storage' => 'Penyimpanan',
            'other' => 'Lainnya',
        ];
    }

    public static function conditionOptions(): array
    {
        return [
            'Baik' => 'Baik',
            'Rusak Ringan' => 'Rusak Ringan',
            'Rusak Berat' => 'Rusak Berat',
        ];
    }

    public static function conditionFilterValues(string $condition): array
    {
        return match (strtoupper(trim($condition))) {
            'GOOD', 'BAIK' => ['Baik', self::CONDITION_GOOD],
            'LIGHT', 'LIGHT_DAMAGE', 'FAIR', 'CUKUP', 'RUSAK_RINGAN', 'RUSAK RINGAN', 'RINGAN' => ['Rusak Ringan', self::CONDITION_LIGHT],
            'HEAVY', 'HEAVY_DAMAGE', 'POOR', 'BURUK', 'DAMAGED', 'RUSAK', 'RUSAK_BERAT', 'RUSAK BERAT', 'BERAT' => ['Rusak Berat', self::CONDITION_HEAVY],
            default => [$condition],
        };
    }

    public static function statusFilterValues(string $status): array
    {
        return match ($status) {
            'allocated' => ['Teralokasi', self::STATUS_ALLOCATED],
            'in_process' => [
                'Pengajuan Alokasi',
                'Penetapan Pengembalian',
                'Pengajuan Pengembalian',
                'Siap Dialokasikan',
                self::STATUS_READY_TO_ALLOCATE,
            ],
            'unavailable' => ['Tidak Dapat Dialokasikan', self::STATUS_NOT_ALLOCATABLE],
            default => [$status],
        };
    }

    public static function assetTypeFilterValues(string $group): ?array
    {
        if ($group === 'other') {
            return [];
        }

        return self::assetTypeGroups()[$group] ?? null;
    }

    public static function conditionLabel(string $condition): string
    {
        return match (strtoupper(trim($condition))) {
            'GOOD', 'BAIK' => 'Baik',
            'LIGHT', 'LIGHT_DAMAGE', 'FAIR', 'CUKUP', 'RUSAK_RINGAN', 'RUSAK RINGAN', 'RINGAN' => 'Rusak Ringan',
            'HEAVY', 'HEAVY_DAMAGE', 'POOR', 'BURUK', 'DAMAGED', 'RUSAK', 'RUSAK_BERAT', 'RUSAK BERAT', 'BERAT' => 'Rusak Berat',
            default => $condition,
        };
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
            default => self::statusOptions()[$this->status] ?? ucfirst(strtolower($this->status)),
        };
    }

    public function getConditionLabelAttribute(): string
    {
        return self::conditionLabel($this->condition);
    }

    public function getTicketOptionLabelAttribute(): string
    {
        $holder = trim((string) $this->holder);
        $identifier = $this->serial_number
            ? 'Asset Tag: '.$this->serial_number
            : 'ID: '.$this->id;

        return $this->asset_code.' - '.$this->name.' (Pemegang: '.($holder !== '' ? $holder : 'Belum ada pemegang').') - '.$identifier;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function resolveUserIdByName(?string $name): ?int
    {
        $name = trim((string) $name);
        if ($name === '') {
            return null;
        }

        $normalizedName = self::normalizePersonName($name);
        $firstName = preg_split('/\s+/', $name, 2)[0] ?? $name;

        return User::query()
            ->where(function ($query) use ($name, $firstName): void {
                $query->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
                    ->orWhereRaw('LOWER(name) LIKE ?', ['%'.mb_strtolower($firstName).'%']);
            })
            ->get(['id', 'name'])
            ->first(fn (User $user) => self::normalizePersonName($user->name) === $normalizedName)
            ?->id;
    }

    private static function normalizePersonName(string $name): string
    {
        return preg_replace('/[^\p{L}\p{N}]+/u', '', mb_strtolower(trim($name)));
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
            if ($asset->isDirty('holder') || (empty($asset->user_id) && ! empty($asset->holder))) {
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
