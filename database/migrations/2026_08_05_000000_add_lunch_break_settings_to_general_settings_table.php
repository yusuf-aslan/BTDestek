<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->boolean('is_lunch_break_enabled')->default(true)->after('weekend_tickets_allowed');
            $table->time('lunch_break_start')->default('12:00:00')->after('is_lunch_break_enabled');
            $table->time('lunch_break_end')->default('13:00:00')->after('lunch_break_start');
        });
    }

    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn([
                'is_lunch_break_enabled',
                'lunch_break_start',
                'lunch_break_end',
            ]);
        });
    }
};
