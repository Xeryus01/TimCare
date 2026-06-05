<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AssetsTemplateExport implements FromCollection, WithHeadings
{
    public function collection(): Collection
    {
        return new Collection([
            [
                'NO BMN' => '3100102002-7256',
                'Nama' => 'Dell Latitude 7320',
                'Asset Tag' => '054013000636888000KD3100102002-7256',
                'Tanggal Perolehan' => now()->format('Y-m-d'),
                'Nilai Perolehan' => 30500000,
                'Lokasi Aset' => 'BPS Provinsi Kep. Bangka Belitung',
                'Kode Satker' => '1900',
                'Nama Pegawai' => 'Eka Riezalita Pattinama, S.IP',
                'NIP Pegawai' => '197905132009022006',
                'Jenis Barang / Kategori' => 'Lap Top',
                'Merek' => 'Dell',
                'Kondisi' => 'Baik',
                'Status' => 'Teralokasi',
            ],
        ]);
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
