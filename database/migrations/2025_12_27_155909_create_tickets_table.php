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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number')->unique();
            $table->string('name');
            $table->string('department_room');
            $table->foreignId('category_id')->constrained('categories');
            $table->string('subject');
            $table->text('description');
            $table->enum('status', ['yeni', 'işlemde', 'beklemede', 'çözüldü', 'iptal'])->default('yeni');
            $table->enum('priority', ['düşük', 'orta', 'yüksek', 'acil'])->default('orta');
            $table->string('ip_address')->nullable();
            $table->string('computer_name')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};