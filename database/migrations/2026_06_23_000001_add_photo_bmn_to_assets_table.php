<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom foto nomor BMN, mengikuti pola photo_serial / photo_asset.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('assets', 'photo_bmn')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->string('photo_bmn')->nullable()->after('photo_asset');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('assets', 'photo_bmn')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->dropColumn('photo_bmn');
            });
        }
    }
};
