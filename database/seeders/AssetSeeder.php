<?php

namespace Database\Seeders;

use App\Imports\AssetsImport;
use App\Models\Asset;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use RuntimeException;

class AssetSeeder extends Seeder
{
    private const EXPECTED_ASSET_COUNT = 312;

    public function run(): void
    {
        $file = database_path('data/aset-berwujud-2026-11-05-142737.xlsx');

        if (! is_file($file)) {
            throw new RuntimeException("Asset import workbook not found: {$file}");
        }

        DB::transaction(function () use ($file): void {
            $ticketAssetTags = DB::table('tickets')
                ->join('assets', 'tickets.asset_id', '=', 'assets.id')
                ->whereNotNull('tickets.asset_id')
                ->pluck('assets.serial_number', 'tickets.id');

            DB::table('tickets')
                ->whereNotNull('asset_id')
                ->update(['asset_id' => null]);

            DB::table('assets')->delete();

            Excel::import(new AssetsImport, $file);

            $assetsByTag = Asset::query()
                ->whereNotNull('serial_number')
                ->pluck('id', 'serial_number');

            foreach ($ticketAssetTags as $ticketId => $assetTag) {
                if ($assetTag !== null && isset($assetsByTag[$assetTag])) {
                    DB::table('tickets')
                        ->where('id', $ticketId)
                        ->update(['asset_id' => $assetsByTag[$assetTag]]);
                }
            }

            $assetCount = Asset::query()->count();
            if ($assetCount !== self::EXPECTED_ASSET_COUNT) {
                throw new RuntimeException(
                    'Expected '.self::EXPECTED_ASSET_COUNT." imported assets, got {$assetCount}."
                );
            }

            Cache::forget('assets.all');
        });
    }
}
