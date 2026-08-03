<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->text('remote_support_download_url')->nullable()->after('ip_display_position');
            $table->text('remote_support_guide_url')->nullable()->after('remote_support_download_url');
        });
    }

    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn([
                'remote_support_download_url',
                'remote_support_guide_url',
            ]);
        });
    }
};
