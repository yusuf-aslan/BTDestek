<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('topic')->nullable();
            $table->text('description')->nullable();
            $table->string('audience')->default('public');
            $table->string('source_type')->default('upload');
            $table->string('video_path')->nullable();
            $table->string('video_url')->nullable();
            $table->string('poster_path')->nullable();
            $table->string('subtitle_path')->nullable();
            $table->boolean('is_published')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedInteger('views')->default(0);
            $table->timestamps();

            $table->index(['is_published', 'audience']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_videos');
    }
};
