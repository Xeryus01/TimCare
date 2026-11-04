<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['tickets', 'reservations'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (! Schema::hasColumn($tableName, 'service_speed_rating')) {
                    $table->unsignedTinyInteger('service_speed_rating')->nullable()->after('service_rating');
                }
                if (! Schema::hasColumn($tableName, 'service_quality_rating')) {
                    $table->unsignedTinyInteger('service_quality_rating')->nullable()->after('service_speed_rating');
                }
                if (! Schema::hasColumn($tableName, 'service_communication_rating')) {
                    $table->unsignedTinyInteger('service_communication_rating')->nullable()->after('service_quality_rating');
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['tickets', 'reservations'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                foreach (['service_speed_rating', 'service_quality_rating', 'service_communication_rating'] as $column) {
                    if (Schema::hasColumn($tableName, $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
