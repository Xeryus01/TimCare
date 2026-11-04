<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            if (! Schema::hasColumn('logs', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('meta');
            }
            if (! Schema::hasColumn('logs', 'user_agent')) {
                $table->string('user_agent', 500)->nullable()->after('ip_address');
            }
            if (! Schema::hasColumn('logs', 'request_method')) {
                $table->string('request_method', 10)->nullable()->after('user_agent');
            }
            if (! Schema::hasColumn('logs', 'request_path')) {
                $table->string('request_path', 500)->nullable()->after('request_method');
            }
        });
    }

    public function down(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            foreach (['ip_address', 'user_agent', 'request_method', 'request_path'] as $column) {
                if (Schema::hasColumn('logs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
