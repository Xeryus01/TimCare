<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('photo_serial')->nullable()->after('serial_number');
            $table->string('photo_asset')->nullable()->after('photo_serial');
        });
    }

    public function down()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['photo_serial', 'photo_asset']);
        });
    }
};
