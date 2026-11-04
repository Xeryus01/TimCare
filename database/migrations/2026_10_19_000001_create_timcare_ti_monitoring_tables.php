<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('ti_teams', function (Blueprint $t) {
            $t->id(); $t->string('name')->unique();
            $t->foreignId('leader_user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
        });
        Schema::create('ti_team_user', function (Blueprint $t) {
            $t->foreignId('team_id')->constrained('ti_teams')->cascadeOnDelete();
            $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $t->primary(['team_id','user_id']);
        });
        Schema::create('ti_objects', function (Blueprint $t) {
            $t->id(); $t->string('code',50)->unique(); $t->string('name');
            $t->string('scope',60); $t->string('aspect',100);
            $t->string('manager_label')->nullable(); $t->string('audience')->nullable();
            $t->string('technical_type',100)->nullable(); $t->string('programmer_label')->nullable();
            $t->foreignId('pic_user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->boolean('is_active')->default(true); $t->timestamps();
        });
        Schema::create('ti_object_team', function (Blueprint $t) {
            $t->foreignId('object_id')->constrained('ti_objects')->cascadeOnDelete();
            $t->foreignId('team_id')->constrained('ti_teams')->cascadeOnDelete();
            $t->primary(['object_id','team_id']);
        });
        Schema::create('ti_reports', function (Blueprint $t) {
            $t->id(); $t->foreignId('object_id')->constrained('ti_objects')->restrictOnDelete();
            $t->string('type',20); $t->unsignedSmallInteger('year'); $t->unsignedTinyInteger('period');
            $t->string('scope_snapshot',60); $t->string('aspect_snapshot',100); $t->json('data');
            $t->unsignedInteger('version')->default(1);
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps(); $t->unique(['object_id','type','year','period'],'ti_report_unique_period');
            $t->index(['type','year','period']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('ti_reports'); Schema::dropIfExists('ti_object_team');
        Schema::dropIfExists('ti_objects'); Schema::dropIfExists('ti_team_user'); Schema::dropIfExists('ti_teams');
    }
};
