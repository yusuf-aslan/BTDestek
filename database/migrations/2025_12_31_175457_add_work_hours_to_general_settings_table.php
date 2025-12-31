<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->time('work_hours_start')->default('08:00');
            $table->time('work_hours_end')->default('17:00');
            $table->boolean('allow_tickets_outside_work_hours')->default(true);
            $table->boolean('weekend_tickets_allowed')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn([
                'work_hours_start',
                'work_hours_end',
                'allow_tickets_outside_work_hours',
                'weekend_tickets_allowed',
            ]);
        });
    }
};