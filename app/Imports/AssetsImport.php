<?php

namespace App\Imports;

use App\Models\Asset;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AssetsImport implements ToCollection, WithHeadingRow
{
    protected array $headerMap = [
        'asset_code' => ['no_bmn', 'no bmn', 'asset_code', 'kode_aset', 'kode aset', 'kode asset'],
        'name' => ['name', 'nama'],
        'type' => ['type', 'tipe', 'jenis barang / kategori', 'jenis_barang / kategori', 'jenis barang/kategori', 'jenis_barang/kategori'],
        'brand' => ['brand', 'merek'],
        'model' => ['model'],
        'serial_number' => ['asset_tag', 'asset tag', 'serial_number', 'nomor_seri', 'nomor seri'],
        'specs' => ['specs', 'spesifikasi', 'spesifikasi tambahan'],
        'nilai_perolehan' => ['nilai_perolehan', 'nilai perolehan', 'harga perolehan'],
        'kode_satker' => ['kode_satker', 'kode satker'],
        'nip_pegawai' => ['nip_pegawai', 'nip pegawai', 'nip'],
        'location' => ['location', 'lokasi', 'lokasi aset', 'lokasi_aset'],
        'holder' => ['holder', 'pemegang', 'nama pegawai', 'nama_pegawai'],
        'status' => ['status'],
        'condition' => ['condition', 'kondisi'],
        'purchased_at' => ['purchased_at', 'tanggal_dibeli', 'tanggal dibeli', 'tanggal perolehan', 'tanggal_perolehan', 'purchase_date'],
    ];

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $row = $this->normalizeRow($row->toArray());

            $assetCode = trim($this->getValue($row, 'asset_code', ''));
            $assetTag = trim($this->getValue($row, 'serial_number', ''));

            $status = $this->normalizeStatus($this->getValue($row, 'status', Asset::STATUS_ALLOCATED));
            $condition = $this->getValue($row, 'condition', 'GOOD');

            $holder = trim($this->getValue($row, 'holder', ''));
            if ($holder === '' || Str::upper($holder) === 'BELUM DIALOKASIKAN') {
                $holder = null;
            }

            $data = [
                'name' => trim($this->getValue($row, 'name', '')),
                'type' => trim($this->getValue($row, 'type', '')),
                'brand' => trim($this->getValue($row, 'brand', '')),
                'model' => trim($this->getValue($row, 'model', '')),
                'serial_number' => trim($this->getValue($row, 'serial_number', '')),
                'specs' => $this->normalizeSpecs($this->getValue($row, 'specs', '')),
                'location' => trim($this->getValue($row, 'location', '')),
                'holder' => $holder,
                'status' => $status,
                'condition' => Asset::normalizeCondition($condition),
                'purchased_at' => $this->normalizeDate($this->getValue($row, 'purchased_at', null)),
                'nilai_perolehan' => $this->normalizeAmount($this->getValue($row, 'nilai_perolehan', null)),
                'kode_satker' => trim($this->getValue($row, 'kode_satker', '')) ?: null,
                'nip_pegawai' => trim($this->getValue($row, 'nip_pegawai', '')) ?: null,
            ];

            $unknownValues = $this->extractUnknownColumns($row);
            if (!empty($unknownValues)) {
                $data['specs'] = $this->mergeSpecs($data['specs'], $unknownValues);
            }

            // Normalize keys
            $assetCodeNormalized = trim($assetCode);
            $assetTagNormalized = trim($assetTag);

            // Only match existing records by serial_number (asset tag).
            // Do NOT update existing records based solely on asset_code to avoid overwriting
            // multiple rows that share the same NO BMN in the import file.
            $existingAsset = null;
            if ($assetTagNormalized !== '') {
                $existingAsset = Asset::where('serial_number', $assetTagNormalized)->first();
            }

            if ($existingAsset) {
                $existingAsset->update(array_merge([
                    'asset_code' => $assetCodeNormalized ?: $existingAsset->asset_code,
                    'serial_number' => $assetTagNormalized ?: $existingAsset->serial_number,
                ], $data));
            } else {
                // Prepare create payload. Use asset_code if provided, otherwise fallback to asset_tag.
                $newAssetData = array_merge([
                    'asset_code' => $assetCodeNormalized ?: $assetTagNormalized,
                    'serial_number' => $assetTagNormalized ?: null,
                ], $data);

                // Ensure asset_code uniqueness by appending suffix only if collision still occurs
                $baseCode = $newAssetData['asset_code'] ?: ('ASSET-' . uniqid());
                $attempt = 0;
                while (Asset::where('asset_code', $newAssetData['asset_code'])->exists()) {
                    $attempt++;
                    $newAssetData['asset_code'] = $baseCode . '-' . $attempt;
                    if ($attempt > 100) {
                        break;
                    }
                }

                Asset::create($newAssetData);
            }
        }
    }
    

    protected function normalizeRow(array $row): array
    {
        $normalized = [];

        foreach ($row as $key => $value) {
            $normalized[$this->normalizeHeaderKey((string) $key)] = $value;
        }

        return $normalized;
    }

    protected function getValue(array $row, string $field, $default = null): string
    {
        foreach ($this->headerMap[$field] as $key) {
            $normalizedKey = $this->normalizeHeaderKey($key);

            if (array_key_exists($normalizedKey, $row) && trim((string) $row[$normalizedKey]) !== '') {
                return trim((string) $row[$normalizedKey]);
            }
        }

        return $default === null ? '' : (string) $default;
    }

    protected function normalizeHeaderKey(string $key): string
    {
        return Str::of($key)
            ->trim()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '_')
            ->replaceMatches('/_+/', '_')
            ->trim('_')
            ->__toString();
    }

    protected function normalizeStatus(?string $status): string
    {
        $value = strtoupper(trim((string) $status));

        return match ($value) {
            'ACTIVE', 'AKTIF', 'TERALOKASI', 'TERALOKASIKAN', 'DIALOKASI', 'DIALOKASIKAN' => Asset::STATUS_ALLOCATED,
            'PENDING', 'MENUNGGU', 'SIAP DIALOKASIKAN', 'SIAP_DIALOKASIKAN', 'SIAP DIALOKASI' => Asset::STATUS_READY_TO_ALLOCATE,
            'INACTIVE', 'NONAKTIF', 'TIDAKAKTIF', 'TIDAK DAPAT DIALOKASIKAN', 'TIDAK_DAPAT_DIALOKASIKAN', 'TIDAK DAPAT DIAKOLASIKAN', 'TIDAK DAPAT DIALOKASIKAN' => Asset::STATUS_NOT_ALLOCATABLE,
            'MAINTENANCE', 'BROKEN', 'RETIRED', 'DECOMMISSIONED' => Asset::STATUS_NOT_ALLOCATABLE,
            default => Asset::STATUS_ALLOCATED,
        };
    }

    protected function extractUnknownColumns(array $row): array
    {
        $knownKeys = [];
        foreach ($this->headerMap as $keys) {
            foreach ($keys as $key) {
                $knownKeys[] = Str::of($key)
                    ->trim()
                    ->lower()
                    ->replaceMatches('/[\s\t\r\n]+/', '_')
                    ->__toString();
            }
        }

        $knownKeys = array_unique($knownKeys);
        $unknown = [];

        foreach ($row as $key => $value) {
            if ($value === null || trim((string) $value) === '') {
                continue;
            }

            if (in_array($key, $knownKeys, true)) {
                continue;
            }

            $unknown[$key] = trim((string) $value);
        }

        return $unknown;
    }

    protected function normalizeSpecs($value): ?string
    {
        if (is_array($value)) {
            $value = json_encode($value);
        } else {
            $value = trim((string) $value);
        }

        if ($value === '') {
            return null;
        }

        if (Str::startsWith($value, '{') && Str::endsWith($value, '}')) {
            return $value;
        }

        return json_encode(array_filter(array_map('trim', explode(',', $value))));
    }

    protected function mergeSpecs(?string $existingSpecs, array $extras): ?string
    {
        $specs = [];

        if ($existingSpecs !== null && $existingSpecs !== '') {
            $decoded = json_decode($existingSpecs, true);
            if (is_array($decoded)) {
                $specs = $decoded;
            } else {
                $specs = array_filter(array_map('trim', explode(',', $existingSpecs)));
            }
        }

        foreach ($extras as $key => $value) {
            $specs[$key] = $value;
        }

        return empty($specs) ? null : json_encode($specs);
    }

    protected function normalizeAmount($value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        $clean = preg_replace('/[^0-9,.-]/', '', $value);
        if ($clean === '') {
            return null;
        }

        if (strpos($clean, ',') !== false && strpos($clean, '.') !== false) {
            if (strrpos($clean, '.') > strrpos($clean, ',')) {
                $clean = str_replace(',', '', $clean);
            } else {
                $clean = str_replace('.', '', $clean);
                $clean = str_replace(',', '.', $clean);
            }
        } elseif (strpos($clean, ',') !== false) {
            $clean = str_replace(',', '.', $clean);
        }

        return $clean;
    }

    protected function normalizeDate($value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
