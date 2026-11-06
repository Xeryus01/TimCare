<?php

namespace Tests\Feature;

use App\Http\Controllers\TicketViewController;
use App\Models\Asset;
use Database\Seeders\AssetSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class AssetSeederTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->softDeletes();
        });

        Schema::create('assets', function (Blueprint $table): void {
            $table->id();
            $table->string('asset_code', 50);
            $table->string('name', 150);
            $table->string('type', 50);
            $table->string('brand', 80)->nullable();
            $table->string('model', 80)->nullable();
            $table->string('serial_number', 120)->nullable()->unique();
            $table->string('photo_serial')->nullable();
            $table->string('photo_asset')->nullable();
            $table->string('photo_bmn')->nullable();
            $table->json('specs')->nullable();
            $table->string('location', 120)->nullable();
            $table->string('holder', 120)->nullable();
            $table->string('status', 30)->default('ACTIVE');
            $table->string('condition', 30)->default('GOOD');
            $table->date('purchased_at')->nullable();
            $table->decimal('nilai_perolehan', 18, 2)->nullable();
            $table->string('kode_satker', 50)->nullable();
            $table->string('nip_pegawai', 30)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });

        Schema::create('tickets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('asset_id')
                ->nullable()
                ->constrained('assets')
                ->cascadeOnDelete();
        });
    }

    public function test_ticket_asset_options_show_holders_first_and_include_them_in_labels(): void
    {
        Asset::create([
            'asset_code' => 'UNASSIGNED',
            'name' => 'Unassigned workstation',
            'type' => 'PC Workstation',
            'serial_number' => 'TAG-UNASSIGNED',
            'status' => Asset::STATUS_READY_TO_ALLOCATE,
            'condition' => Asset::CONDITION_GOOD,
        ]);
        Asset::create([
            'asset_code' => 'ASSIGNED',
            'name' => 'Assigned workstation',
            'type' => 'PC Workstation',
            'serial_number' => 'TAG-ASSIGNED',
            'holder' => 'Zahra Pemegang',
            'status' => Asset::STATUS_ALLOCATED,
            'condition' => Asset::CONDITION_GOOD,
        ]);
        Cache::forget('assets.all');

        $view = $this->app->make(TicketViewController::class)->create(Request::create('/tickets/create'));
        $assets = $view->getData()['assets'];

        $this->assertSame('ASSIGNED', $assets->first()->asset_code);
        $this->assertSame('UNASSIGNED', $assets->last()->asset_code);
        $this->assertSame(
            'ASSIGNED - Assigned workstation (Pemegang: Zahra Pemegang) - Asset Tag: TAG-ASSIGNED',
            $assets->first()->ticket_option_label
        );
        $this->assertSame(
            'UNASSIGNED - Unassigned workstation (Pemegang: Belum ada pemegang) - Asset Tag: TAG-UNASSIGNED',
            $assets->last()->ticket_option_label
        );
    }

    public function test_asset_filters_use_a_small_set_of_categories_and_status_groups(): void
    {
        $typeGroups = Asset::assetTypeGroups();
        $knownTypes = array_merge(...array_values($typeGroups));
        $workbook = IOFactory::load(database_path('data/aset-berwujud-2026-11-05-142737.xlsx'))
            ->getActiveSheet();

        for ($row = 2; $row <= $workbook->getHighestDataRow(); $row++) {
            $type = trim((string) $workbook->getCell("J{$row}")->getFormattedValue());
            $this->assertNotSame('', $type);
            $this->assertContains($type, $knownTypes);
        }

        $this->assertCount(5, Asset::assetTypeGroupOptions());
        $this->assertCount(3, Asset::statusFilterOptions());
        $this->assertCount(3, Asset::conditionOptions());
        $this->assertContains('Pengajuan Alokasi', Asset::statusFilterValues('in_process'));
        $this->assertContains('Pengajuan Pengembalian', Asset::statusFilterValues('in_process'));
        $this->assertContains(Asset::STATUS_ALLOCATED, Asset::statusFilterValues('allocated'));
        $this->assertContains(Asset::STATUS_NOT_ALLOCATABLE, Asset::statusFilterValues('unavailable'));
        $this->assertSame(
            ['External', 'External/ Portable Hardisk', 'Hard Disk', 'Rak Server', 'Storage Modul Disk (Peralatan Mainframe)'],
            Asset::assetTypeFilterValues('storage')
        );
    }

    public function test_it_replaces_the_inventory_and_preserves_excel_values(): void
    {
        Asset::create([
            'asset_code' => 'OLD-ASSET',
            'name' => 'Old asset',
            'type' => 'PC Workstation',
            'status' => Asset::STATUS_ALLOCATED,
            'condition' => Asset::CONDITION_GOOD,
        ]);

        DB::table('users')->insert(['name' => 'Ruangan: Ruang IPDS - Ruang Medsis Diseminasi']);
        $staffUserId = DB::table('users')->insertGetId(['name' => 'Ani Pertiwi, S.Si, M.Stat']);

        $this->seed(AssetSeeder::class);
        $this->seed(AssetSeeder::class);

        $this->assertSame(312, Asset::count());
        $this->assertDatabaseMissing('assets', ['asset_code' => 'OLD-ASSET']);
        $this->assertSame(241, Asset::where('status', 'Teralokasi')->count());
        $this->assertSame(20, Asset::where('status', 'Tidak Dapat Dialokasikan')->count());
        $this->assertSame(29, Asset::where('status', 'Pengajuan Alokasi')->count());
        $this->assertSame(1, Asset::where('status', 'Penetapan Pengembalian')->count());
        $this->assertSame(2, Asset::where('status', 'Pengajuan Pengembalian')->count());
        $this->assertSame(19, Asset::where('status', 'Siap Dialokasikan')->count());
        $this->assertSame([
            'Teralokasi',
            'Tidak Dapat Dialokasikan',
            'Pengajuan Alokasi',
            'Penetapan Pengembalian',
            'Pengajuan Pengembalian',
            'Siap Dialokasikan',
        ], array_slice(array_values(Asset::statusOptions()), 0, 6));
        $this->assertSame(119, Asset::where('condition', 'Baik')->count());
        $this->assertSame(144, Asset::where('condition', 'Rusak Ringan')->count());
        $this->assertSame(49, Asset::where('condition', 'Rusak Berat')->count());
        $this->assertSame(['Baik', 'Rusak Ringan', 'Rusak Berat'], array_values(Asset::conditionOptions()));
        $this->assertSame(244, Asset::whereNull('nip_pegawai')->count());
        $this->assertSame(
            312,
            Asset::whereNotNull('serial_number')->distinct()->count('serial_number')
        );

        $workstation = Asset::where('asset_code', '3100101007-1')->firstOrFail();
        $this->assertSame('Rusak Ringan', $workstation->condition);
        $this->assertSame('Teralokasi', $workstation->status);
        $this->assertSame('Rusak Ringan', $workstation->condition_label);
        $this->assertSame('Teralokasi', $workstation->status_label);

        $workbook = IOFactory::load(database_path('data/aset-berwujud-2026-11-05-142737.xlsx'))
            ->getActiveSheet();
        $canonicalHolder = null;
        $canonicalNip = null;
        for ($row = 2; $row <= $workbook->getHighestDataRow(); $row++) {
            if ($workbook->getCell("A{$row}")->getValue() === '3100203017-2') {
                $canonicalHolder = $workbook->getCell("H{$row}")->getValue();
                $canonicalNip = preg_replace('/\D+/', '', (string) $workbook->getCell("I{$row}")->getFormattedValue());
                break;
            }
        }

        $reconciledAsset = Asset::where('asset_code', '3100203017-2')->firstOrFail();
        $this->assertNotNull($canonicalHolder);
        $this->assertSame($canonicalHolder, $reconciledAsset->holder);
        $this->assertSame($canonicalNip, $reconciledAsset->nip_pegawai);
        $staffAsset = Asset::where('nip_pegawai', '198405022009022012')->firstOrFail();
        $this->assertSame('Ani Pertiwi S.Si, M.Stat', $staffAsset->holder);
        $this->assertSame($staffUserId, $staffAsset->user_id);
    }

    public function test_it_keeps_tickets_and_relinks_them_by_asset_tag_when_possible(): void
    {
        $linkedAsset = Asset::create([
            'asset_code' => 'OLD-1',
            'name' => 'Old matching asset',
            'type' => 'PC Workstation',
            'serial_number' => '054013000636888000KD3100101007-1',
            'status' => Asset::STATUS_ALLOCATED,
            'condition' => Asset::CONDITION_GOOD,
        ]);
        $removedAsset = Asset::create([
            'asset_code' => 'OLD-2',
            'name' => 'Asset not in workbook',
            'type' => 'PC Workstation',
            'serial_number' => 'old-asset-tag',
            'status' => Asset::STATUS_ALLOCATED,
            'condition' => Asset::CONDITION_GOOD,
        ]);
        $linkedTicketId = DB::table('tickets')->insertGetId(['asset_id' => $linkedAsset->id]);
        $detachedTicketId = DB::table('tickets')->insertGetId(['asset_id' => $removedAsset->id]);

        $this->seed(AssetSeeder::class);

        $newAssetId = Asset::where('serial_number', $linkedAsset->serial_number)->value('id');
        $this->assertNotNull($newAssetId);
        $this->assertDatabaseHas('tickets', [
            'id' => $linkedTicketId,
            'asset_id' => $newAssetId,
        ]);
        $this->assertDatabaseHas('tickets', [
            'id' => $detachedTicketId,
            'asset_id' => null,
        ]);
        $this->assertSame(2, DB::table('tickets')->count());
    }

    public function test_ticket_foreign_key_preserves_tickets_when_an_asset_is_deleted(): void
    {
        $asset = Asset::create([
            'asset_code' => 'DELETE-ME',
            'name' => 'Asset to delete',
            'type' => 'PC Workstation',
            'status' => Asset::STATUS_ALLOCATED,
            'condition' => Asset::CONDITION_GOOD,
        ]);
        $ticketId = DB::table('tickets')->insertGetId(['asset_id' => $asset->id]);
        Schema::table('tickets', function (Blueprint $table): void {
            $table->dropForeign(['asset_id']);
        });
        $orphanTicketId = DB::table('tickets')->insertGetId(['asset_id' => 999999]);
        $migration = require database_path(
            'migrations/2026_11_05_000001_preserve_tickets_when_assets_are_deleted.php'
        );

        $migration->up();
        $migration->up();

        $this->assertDatabaseHas('tickets', [
            'id' => $orphanTicketId,
            'asset_id' => null,
        ]);

        DB::table('assets')->where('id', $asset->id)->delete();

        $this->assertDatabaseHas('tickets', [
            'id' => $ticketId,
            'asset_id' => null,
        ]);

        $migration->down();
    }
}
