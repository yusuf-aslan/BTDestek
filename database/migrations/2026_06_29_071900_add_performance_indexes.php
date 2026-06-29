<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // En sık sorgulanan filtre sütunları
            $table->index('status', 'tickets_status_idx');
            $table->index('assigned_to', 'tickets_assigned_to_idx');
            $table->index('resolved_by', 'tickets_resolved_by_idx');
            $table->index('created_at', 'tickets_created_at_idx');
            $table->index('ip_address', 'tickets_ip_address_idx');

            // Raporlar sayfasında sıkça kullanılan bileşik sorgu
            $table->index(['resolved_by', 'status'], 'tickets_resolved_by_status_idx');

            // TicketWatcher sorgusu: status + assigned_to
            $table->index(['status', 'assigned_to'], 'tickets_status_assigned_idx');
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->index('user_id', 'activities_user_id_idx');
            $table->index('activity_date', 'activities_date_idx');
            $table->index(['user_id', 'activity_date'], 'activities_user_date_idx');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex('tickets_status_idx');
            $table->dropIndex('tickets_assigned_to_idx');
            $table->dropIndex('tickets_resolved_by_idx');
            $table->dropIndex('tickets_created_at_idx');
            $table->dropIndex('tickets_ip_address_idx');
            $table->dropIndex('tickets_resolved_by_status_idx');
            $table->dropIndex('tickets_status_assigned_idx');
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex('activities_user_id_idx');
            $table->dropIndex('activities_date_idx');
            $table->dropIndex('activities_user_date_idx');
        });
    }
};
