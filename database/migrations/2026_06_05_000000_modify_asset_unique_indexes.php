<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        // Drop unique index on asset_code if exists
        if ($driver === 'sqlite') {
            DB::statement('DROP INDEX IF EXISTS assets_asset_code_unique');
        } else {
            Schema::table('assets', function (Blueprint $table) {
                // index name created by Laravel for unique on asset_code is assets_asset_code_unique
                $table->dropUnique('assets_asset_code_unique');
            });
        }

        // Add unique index on serial_number
        Schema::table('assets', function (Blueprint $table) {
            $table->unique('serial_number', 'assets_serial_number_unique');
        });
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        // Drop unique on serial_number
        if ($driver === 'sqlite') {
            DB::statement('DROP INDEX IF EXISTS assets_serial_number_unique');
        } else {
            Schema::table('assets', function (Blueprint $table) {
                $table->dropUnique('assets_serial_number_unique');
            });
        }

        // Restore unique on asset_code
        Schema::table('assets', function (Blueprint $table) {
            $table->unique('asset_code', 'assets_asset_code_unique');
        });
    }
};
