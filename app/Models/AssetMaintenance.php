<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetMaintenance extends Model
{
    protected $fillable = [
        'asset_id',
        'type',
        'maintenance_date',
        'description',
        'findings',
        'actions_taken',
        'status',
        'condition_before',
        'condition_after',
        'performed_by_user_id',
        'next_maintenance_date',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'next_maintenance_date' => 'datetime',
    ];

    const TYPE_PREVENTIVE = 'PREVENTIVE';

    const TYPE_CORRECTIVE = 'CORRECTIVE';

    const TYPE_INSPECTION = 'INSPECTION';

    public static function types(): array
    {
        return [
            self::TYPE_PREVENTIVE => 'Perawatan Preventif',
            self::TYPE_CORRECTIVE => 'Perawatan Korektif',
            self::TYPE_INSPECTION => 'Inspeksi',
        ];
    }

    public function getTypeLabel(): string
    {
        return self::types()[$this->type] ?? $this->type;
    }

    public function getConditionBeforeLabelAttribute(): ?string
    {
        if (! $this->condition_before) {
            return null;
        }

        return Asset::conditionLabel($this->condition_before);
    }

    public function getConditionAfterLabelAttribute(): ?string
    {
        if (! $this->condition_after) {
            return null;
        }

        return Asset::conditionLabel($this->condition_after);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function performedByUser()
    {
        return $this->belongsTo(User::class, 'performed_by_user_id');
    }
}
