<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('ticket_statuses')
            ->where('closes_ticket', true)
            ->where('destination', 'pending')
            ->update(['destination' => 'solved', 'updated_at' => now()]);
    }

    public function down(): void
    {
        // Kullanıcının sekme tercihlerini geriye doğru tahmin etmek güvenli değildir.
    }
};
