<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('partner_name');
            $table->string('partner_slug')->unique();
            $table->string('partner_phone');
            $table->string('partner_email');
            $table->text('partner_description');
            $table->string('partner_image');
            $table->string('partner_address');
            $table->string('partner_status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
