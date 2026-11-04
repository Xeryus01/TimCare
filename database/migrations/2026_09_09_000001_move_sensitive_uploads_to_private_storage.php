<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        $paths = collect();

        if (Schema::hasTable('attachments')) {
            $paths = $paths->merge(
                DB::table('attachments')->whereNotNull('file_path')->pluck('file_path')
            );
        }

        if (Schema::hasTable('reservations') && Schema::hasColumn('reservations', 'nota_dinas_path')) {
            $paths = $paths->merge(
                DB::table('reservations')->whereNotNull('nota_dinas_path')->pluck('nota_dinas_path')
            );
        }

        foreach ($paths->filter()->unique() as $path) {
            $this->move($path, 'public', 'local');
        }
    }

    public function down(): void
    {
        // Berkas sensitif sengaja tetap privat ketika rollback skema dilakukan.
    }

    private function move(string $path, string $fromDisk, string $toDisk): void
    {
        if (str_contains($path, '..')) {
            return;
        }

        if (! Storage::disk($fromDisk)->exists($path)) {
            // Beberapa deployment cPanel lama memakai direktori public/storage
            // biasa, bukan symbolic link Laravel.
            $legacyPublicPath = public_path('storage/' . ltrim($path, '/'));
            if (is_file($legacyPublicPath) && ! Storage::disk($toDisk)->exists($path)) {
                Storage::disk($toDisk)->put($path, file_get_contents($legacyPublicPath));
            }

            if (is_file($legacyPublicPath) && Storage::disk($toDisk)->exists($path)) {
                @unlink($legacyPublicPath);
            }

            return;
        }

        if (! Storage::disk($toDisk)->exists($path)) {
            Storage::disk($toDisk)->put($path, Storage::disk($fromDisk)->get($path));
        }

        if (Storage::disk($toDisk)->exists($path)) {
            Storage::disk($fromDisk)->delete($path);
        }
    }
};