<?php

namespace App\Imports;

use App\Models\Asset;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class AssetsExport implements FromCollection, WithHeadings
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate ? Carbon::parse($startDate) : null;
        $this->endDate = $endDate ? Carbon::parse($endDate) : null;
    }

    public function collection(): Collection
    {
        $query = Asset::with(['holderHistory.changedByUser', 'maintenances.performedByUser', 'logs.actor']);

        if ($this->startDate && $this->endDate) {
            $query->where(function ($query) {
                $query->whereNull('purchased_at')
                    ->orWhere('purchased_at', '<=', $this->endDate);
            });
        }

        return $query->get()->map(function ($asset) {
            return [
                'asset_code' => $asset->asset_code,
                'name' => $asset->name,
                'serial_number' => $asset->serial_number,
                'purchased_at' => $asset->purchased_at ? $asset->purchased_at->format('Y-m-d') : '',
                'nilai_perolehan' => $asset->nilai_perolehan,
                'location' => $asset->location,
                'kode_satker' => $asset->kode_satker,
                'holder' => $this->resolveHolder($asset),
                'nip_pegawai' => $asset->nip_pegawai,
                'type' => $asset->type,
                'brand' => $asset->brand,
                'condition' => $this->resolveCondition($asset),
                'status' => $asset->status_label,
            ];
        });
    }

    protected function resolveHolder(Asset $asset): ?string
    {
        if (! $this->endDate) {
            return $asset->holder;
        }

        $history = $asset->holderHistory
            ->filter(function ($history) {
                return $history->changed_at && $history->changed_at->lte($this->endDate);
            })
            ->sortByDesc('changed_at')
            ->first();

        if ($history && $history->new_holder) {
            return $history->new_holder;
        }

        return $asset->holder;
    }

    protected function resolveCondition(Asset $asset): string
    {
        if (! $this->endDate) {
            return $asset->condition_label;
        }

        $maintenance = $asset->maintenances
            ->filter(function ($maintenance) {
                return $maintenance->maintenance_date && $maintenance->maintenance_date->lte($this->endDate);
            })
            ->sortByDesc('maintenance_date')
            ->first();

        if ($maintenance && $maintenance->condition_after) {
            return Asset::conditionOptions()[Asset::normalizeCondition($maintenance->condition_after)] ?? $maintenance->condition_after;
        }

        return $asset->condition_label;
    }

    public function headings(): array
    {
        return [
            'NO BMN',
            'Nama',
            'Asset Tag',
            'Tanggal Perolehan',
            'Nilai Perolehan',
            'Lokasi Aset',
            'Kode Satker',
            'Nama Pegawai',
            'NIP Pegawai',
            'Jenis Barang / Kategori',
            'Merek',
            'Kondisi',
            'Status',
        ];
    }
}
