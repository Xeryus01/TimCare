<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('tickets')
            ->whereNotNull('asset_id')
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('assets')
                    ->whereColumn('assets.id', 'tickets.asset_id');
            })
            ->update(['asset_id' => null]);

        $foreignKey = collect(Schema::getForeignKeys('tickets'))->first(
            fn (array $key): bool => $key['columns'] === ['asset_id']
                && $key['foreign_table'] === 'assets'
                && $key['foreign_columns'] === ['id']
        );

        if ($foreignKey && $foreignKey['on_delete'] === 'set null') {
            return;
        }

        if ($foreignKey) {
            Schema::table('tickets', function (Blueprint $table) use ($foreignKey) {
                $table->dropForeign($foreignKey['name'] ?? ['asset_id']);
            });
        }

        Schema::table('tickets', function (Blueprint $table) {
            $table->foreign('asset_id')
                ->references('id')
                ->on('assets')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        $foreignKey = collect(Schema::getForeignKeys('tickets'))->first(
            fn (array $key): bool => $key['columns'] === ['asset_id']
                && $key['foreign_table'] === 'assets'
                && $key['foreign_columns'] === ['id']
        );

        if ($foreignKey && $foreignKey['on_delete'] === 'cascade') {
            return;
        }

        if ($foreignKey) {
            Schema::table('tickets', function (Blueprint $table) use ($foreignKey) {
                $table->dropForeign($foreignKey['name'] ?? ['asset_id']);
            });
        }

        Schema::table('tickets', function (Blueprint $table) {
            $table->foreign('asset_id')
                ->references('id')
                ->on('assets')
                ->cascadeOnDelete();
        });
    }
};
