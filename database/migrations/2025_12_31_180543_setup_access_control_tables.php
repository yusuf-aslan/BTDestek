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
        // 1. Pivot table for User-Category relationship
        Schema::create('category_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // 2. Add resolved_by to tickets
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('resolved_by')->nullable()->constrained('users');
        });

        // 3. Add is_admin to users (simple role management)
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_user');

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['resolved_by']);
            $table->dropColumn('resolved_by');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};