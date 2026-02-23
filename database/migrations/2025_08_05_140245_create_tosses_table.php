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
        Schema::create('tosses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // ticket info fields
            $table->string('ticket_code');
            
            // Document info fields
            $table->string('document_name');
            $table->text('document_description')->nullable();
            $table->string('document_bySign')->nullable();
            $table->string('document_toReceive')->nullable();
            $table->string('document_action')->nullable();
            $table->string('document_no')->nullable();
            $table->text('document_notes')->nullable();
            
            // File paths
            $table->string('document_path')->nullable();
            $table->string('qr_path')->nullable();
            
            // QR position data
            $table->float('qr_position_x', 8, 2)->nullable();
            $table->float('qr_position_y', 8, 2)->nullable();
            $table->float('qr_scale', 8, 2)->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tosses');
    }
};