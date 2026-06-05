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
            $query->whereBetween('purchased_at', [$this->startDate, $this->endDate]);
        }

        return $query->get()->map(function ($asset) {
            $holderHistory = $asset->holderHistory->map(function ($history) {
                return [
                    'changed_at' => $history->changed_at ? $history->changed_at->format('Y-m-d H:i:s') : null,
                    'previous_holder' => $history->previous_holder,
                    'new_holder' => $history->new_holder,
                    'changed_by' => optional($history->changedByUser)->name,
                    'notes' => $history->notes,
                ];
            });

            $maintenanceHistory = $asset->maintenances->map(function ($maintenance) {
                return [
                    'maintenance_date' => $maintenance->maintenance_date ? $maintenance->maintenance_date->format('Y-m-d H:i:s') : null,
                    'type' => $maintenance->type,
                    'description' => $maintenance->description,
                    'findings' => $maintenance->findings,
                    'actions_taken' => $maintenance->actions_taken,
                    'performed_by' => optional($maintenance->performedByUser)->name,
                    'condition_before' => $maintenance->condition_before,
                    'condition_after' => $maintenance->condition_after,
                    'next_maintenance_date' => $maintenance->next_maintenance_date ? $maintenance->next_maintenance_date->format('Y-m-d H:i:s') : null,
                ];
            });

            $changeHistory = $asset->logs->map(function ($log) {
                return [
                    'action' => $log->action,
                    'actor' => optional($log->actor)->name,
                    'meta' => $log->meta,
                    'created_at' => $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : null,
                ];
            });

            return [
                'asset_code' => $asset->asset_code,
                'name' => $asset->name,
                'serial_number' => $asset->serial_number,
                'purchased_at' => $asset->purchased_at ? $asset->purchased_at->format('Y-m-d') : '',
                'nilai_perolehan' => $asset->nilai_perolehan,
                'location' => $asset->location,
                'kode_satker' => $asset->kode_satker,
                'holder' => $asset->holder,
                'nip_pegawai' => $asset->nip_pegawai,
                'type' => $asset->type,
                'brand' => $asset->brand,
                'condition' => $asset->condition_label,
                'status' => $asset->status_label,
            ];
        });
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
