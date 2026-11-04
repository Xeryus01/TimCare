<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (! Schema::hasColumn('tickets', 'service_rating')) {
                $table->unsignedTinyInteger('service_rating')->nullable()->after('resolved_at');
            }
            if (! Schema::hasColumn('tickets', 'service_feedback')) {
                $table->text('service_feedback')->nullable()->after('service_rating');
            }
            if (! Schema::hasColumn('tickets', 'rated_at')) {
                $table->timestamp('rated_at')->nullable()->after('service_feedback');
            }
        });

        Schema::table('reservations', function (Blueprint $table) {
            if (! Schema::hasColumn('reservations', 'service_rating')) {
                $table->unsignedTinyInteger('service_rating')->nullable()->after('nota_dinas_path');
            }
            if (! Schema::hasColumn('reservations', 'service_feedback')) {
                $table->text('service_feedback')->nullable()->after('service_rating');
            }
            if (! Schema::hasColumn('reservations', 'rated_at')) {
                $table->timestamp('rated_at')->nullable()->after('service_feedback');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            foreach (['service_rating', 'service_feedback', 'rated_at'] as $column) {
                if (Schema::hasColumn('tickets', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('reservations', function (Blueprint $table) {
            foreach (['service_rating', 'service_feedback', 'rated_at'] as $column) {
                if (Schema::hasColumn('reservations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
