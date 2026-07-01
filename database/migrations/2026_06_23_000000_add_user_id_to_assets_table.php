<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom user_id pada tabel assets untuk mereferensikan pemegang aset
     * ke akun user TimCare berdasarkan kecocokan nama. Lalu lakukan backfill untuk
     * data aset yang sudah ada.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('assets', 'user_id')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('holder');
                $table->index('user_id');
            });
        }

        // Backfill: cocokkan nama pemegang (holder) dengan nama user.
        $map = [];
        foreach (DB::table('users')->select('id', 'name')->get() as $u) {
            $key = mb_strtolower(trim((string) $u->name));
            if ($key !== '') {
                $map[$key] = $u->id;
            }
        }

        if (!empty($map)) {
            foreach (DB::table('assets')->select('id', 'holder')->whereNotNull('holder')->get() as $a) {
                $key = mb_strtolower(trim((string) $a->holder));
                if ($key !== '' && isset($map[$key])) {
                    DB::table('assets')->where('id', $a->id)->update(['user_id' => $map[$key]]);
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('assets', 'user_id')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->dropIndex(['user_id']);
                $table->dropColumn('user_id');
            });
        }
    }
};
