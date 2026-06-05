<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code', 50)->unique();
            $table->string('name', 150);
            $table->string('type', 50);
            $table->string('brand', 80)->nullable();
            $table->string('model', 80)->nullable();
            $table->string('serial_number', 120)->nullable();
            $table->json('specs')->nullable();
            $table->string('location', 120)->nullable();
            $table->string('holder', 120)->nullable();
            $table->string('status', 30)->default('ACTIVE');
            $table->string('condition', 30)->default('GOOD');
            $table->date('purchased_at')->nullable();
            $table->decimal('nilai_perolehan', 18, 2)->nullable();
            $table->string('kode_satker', 50)->nullable();
            $table->string('nip_pegawai', 30)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
