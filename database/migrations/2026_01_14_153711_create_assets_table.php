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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            
            // Identification
            $table->string('name')->nullable()->comment('PC No, Friendly Name or Hostname');
            $table->string('asset_tag')->nullable()->unique()->comment('Demirbaş No / Inventory Number');
            $table->string('serial_number')->nullable();
            
            // Categorization
            $table->string('type')->default('computer')->index()->comment('computer, printer, network, medical, other');
            $table->string('status')->default('active')->index()->comment('active, maintenance, retired, stock, broken');
            
            // Ownership & Location
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('location')->nullable()->comment('Room number or specific location details');
            
            // Technical Details
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->json('specs')->nullable()->comment('Flexible JSON for RAM, CPU, Disk, Monitor etc.');
            
            // Financial & Lifecycle
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expires_at')->nullable();
            
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};