<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('edit_lock_user_id')->nullable();
            $table->uuid('edit_lock_token')->nullable();
            $table->dateTime('edit_lock_expires_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tickets', fn (Blueprint $table) => $table->dropColumn([
            'edit_lock_user_id', 'edit_lock_token', 'edit_lock_expires_at',
        ]));
    }
};
