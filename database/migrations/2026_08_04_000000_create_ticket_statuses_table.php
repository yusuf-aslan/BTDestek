<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_statuses', function (Blueprint $table) {
            $table->string('key', 64)->primary();
            $table->string('name', 80);
            $table->string('color', 24)->default('gray');
            $table->string('destination', 24)->default('pending')->index();
            $table->boolean('closes_ticket')->default(false);
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        DB::table('ticket_statuses')->insert([
            ['key' => 'yeni', 'name' => 'Yeni', 'color' => 'gray', 'destination' => 'pending', 'closes_ticket' => false, 'is_active' => true, 'sort_order' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'işlemde', 'name' => 'İşlemde', 'color' => 'warning', 'destination' => 'pending', 'closes_ticket' => false, 'is_active' => true, 'sort_order' => 20, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'beklemede', 'name' => 'Beklemede', 'color' => 'info', 'destination' => 'pending', 'closes_ticket' => false, 'is_active' => true, 'sort_order' => 30, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'işlem yapıldı', 'name' => 'İşlem Yapıldı', 'color' => 'primary', 'destination' => 'solved', 'closes_ticket' => true, 'is_active' => true, 'sort_order' => 40, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'çözüldü', 'name' => 'Çözüldü', 'color' => 'success', 'destination' => 'solved', 'closes_ticket' => true, 'is_active' => true, 'sort_order' => 50, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'iptal', 'name' => 'İptal', 'color' => 'danger', 'destination' => 'cancelled', 'closes_ticket' => true, 'is_active' => true, 'sort_order' => 60, 'created_at' => now(), 'updated_at' => now()],
        ]);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE tickets MODIFY status VARCHAR(64) NOT NULL DEFAULT 'yeni'");
        } else {
            Schema::table('tickets', fn (Blueprint $table) => $table->string('status', 64)->default('yeni')->change());
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE tickets SET status = 'işlemde' WHERE status NOT IN ('yeni','işlemde','beklemede','işlem yapıldı','çözüldü','iptal')");
            DB::statement("ALTER TABLE tickets MODIFY status ENUM('yeni','işlemde','beklemede','işlem yapıldı','çözüldü','iptal') NOT NULL DEFAULT 'yeni'");
        }
        Schema::dropIfExists('ticket_statuses');
    }
};
