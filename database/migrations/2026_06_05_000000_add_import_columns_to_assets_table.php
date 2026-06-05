<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->decimal('nilai_perolehan', 18, 2)->nullable();
            $table->string('kode_satker', 50)->nullable();
            $table->string('nip_pegawai', 30)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['nilai_perolehan', 'kode_satker', 'nip_pegawai']);
        });
    }
};
