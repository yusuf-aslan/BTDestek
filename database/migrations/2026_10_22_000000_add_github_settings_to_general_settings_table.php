<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->string('github_repo')->nullable();
            $table->string('github_branch')->nullable();
            $table->text('github_token')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn([
                'github_repo',
                'github_branch',
                'github_token',
            ]);
        });
    }
};
