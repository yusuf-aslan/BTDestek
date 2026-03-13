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
        Schema::table('tickets', function (Blueprint $blueprint) {
            $blueprint->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $blueprint->dateTime('assigned_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $blueprint) {
            $blueprint->dropForeign(['assigned_to']);
            $blueprint->dropColumn(['assigned_to', 'assigned_at']);
        });
    }
};
