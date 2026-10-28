<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date')->index();
            $table->date('end_date')->index();
            $table->boolean('is_half_day')->default(false);
            $table->time('closes_at')->nullable();
            $table->text('message')->nullable();
            $table->timestamps();
        });

        Schema::table('general_settings', function (Blueprint $table) {
            $table->boolean('official_holidays_enabled')->default(true);
            $table->boolean('holiday_tickets_allowed')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn(['official_holidays_enabled', 'holiday_tickets_allowed']);
        });

        Schema::dropIfExists('holidays');
    }
};
