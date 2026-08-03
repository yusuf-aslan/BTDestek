<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE tickets MODIFY status ENUM('yeni', 'işlemde', 'beklemede', 'işlem yapıldı', 'çözüldü', 'iptal') NOT NULL DEFAULT 'yeni'");
    }

    public function down(): void
    {
        DB::table('tickets')
            ->where('status', 'işlem yapıldı')
            ->update(['status' => 'işlemde']);

        DB::statement("ALTER TABLE tickets MODIFY status ENUM('yeni', 'işlemde', 'beklemede', 'çözüldü', 'iptal') NOT NULL DEFAULT 'yeni'");
    }
};
