<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ticket_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title', 200);
            $table->longText('description')->nullable();
            $table->string('priority', 20)->default('orta');
            $table->string('status', 20)->default('bekliyor');
            $table->dateTime('due_at')->nullable();
            $table->unsignedInteger('remind_before_minutes')->nullable();
            $table->dateTime('remind_at')->nullable();
            $table->string('repeat', 20)->nullable();
            $table->json('checklist')->nullable();
            $table->json('photos')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('reminded_at')->nullable();
            $table->dateTime('overdue_notified_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status', 'due_at']);
            $table->index(['status', 'remind_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
