<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_video_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color', 7)->default('#2563eb');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('training_videos', function (Blueprint $table) {
            $table->foreignId('training_video_category_id')
                ->nullable()
                ->after('slug')
                ->constrained('training_video_categories')
                ->nullOnDelete();
        });

        // Serbest metin "konu" değerlerini modüllere taşı
        $palette = ['#2563eb', '#16a34a', '#dc2626', '#9333ea', '#d97706', '#0891b2', '#db2777', '#65a30d'];
        $topics = DB::table('training_videos')->whereNotNull('topic')->where('topic', '!=', '')->distinct()->orderBy('topic')->pluck('topic');

        foreach ($topics->values() as $i => $topic) {
            $slug = Str::slug($topic) ?: 'modul';
            $base = $slug;
            for ($n = 2; DB::table('training_video_categories')->where('slug', $slug)->exists(); $n++) {
                $slug = $base.'-'.$n;
            }

            $id = DB::table('training_video_categories')->insertGetId([
                'name' => $topic,
                'slug' => $slug,
                'color' => $palette[$i % count($palette)],
                'sort_order' => $i + 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('training_videos')->where('topic', $topic)->update(['training_video_category_id' => $id]);
        }

        Schema::table('training_videos', function (Blueprint $table) {
            $table->dropColumn('topic');
        });
    }

    public function down(): void
    {
        Schema::table('training_videos', function (Blueprint $table) {
            $table->string('topic')->nullable()->after('slug');
        });

        foreach (DB::table('training_video_categories')->get() as $category) {
            DB::table('training_videos')->where('training_video_category_id', $category->id)->update(['topic' => $category->name]);
        }

        Schema::table('training_videos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('training_video_category_id');
        });

        Schema::dropIfExists('training_video_categories');
    }
};
