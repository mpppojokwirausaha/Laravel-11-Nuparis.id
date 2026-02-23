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
        Schema::create('properties', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('property_name');
            $table->string('property_slug')->unique();
            $table->string('property_type');
            $table->bigInteger('property_price');
            $table->string('property_address');
            $table->text('property_description');
            $table->json('property_image');
            $table->string('property_building_area');
            $table->json('property_fasilities')->nullable();
            $table->json('property_certificate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
