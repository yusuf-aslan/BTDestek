<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_video_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_video_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('topic');
            // Aynı konunun tekrar önerildiğini yakalamak için sadeleştirilmiş başlık
            $table->string('topic_key')->index();
            $table->text('details')->nullable();
            $table->string('name')->nullable();
            $table->string('department')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->unsignedInteger('request_count')->default(1);
            $table->foreignId('training_video_id')->nullable()->constrained()->nullOnDelete();
            $table->text('admin_note')->nullable();
            $table->timestamp('last_requested_at')->nullable();
            $table->timestamps();
        });

        Schema::table('general_settings', function (Blueprint $table) {
            $table->boolean('training_video_suggestions_enabled')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn('training_video_suggestions_enabled');
        });

        Schema::dropIfExists('training_video_suggestions');
    }
};
