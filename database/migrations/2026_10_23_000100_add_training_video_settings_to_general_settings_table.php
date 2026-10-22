<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->boolean('training_videos_enabled')->default(false);
            $table->string('training_videos_menu_title')->default('EĞİTİM VİDEOLARI');
            $table->unsignedInteger('training_videos_menu_sort')->default(4);
            $table->text('training_videos_description')->nullable();
            $table->unsignedInteger('training_videos_max_upload_mb')->default(200);
        });
    }

    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn([
                'training_videos_enabled',
                'training_videos_menu_title',
                'training_videos_menu_sort',
                'training_videos_description',
                'training_videos_max_upload_mb',
            ]);
        });
    }
};
