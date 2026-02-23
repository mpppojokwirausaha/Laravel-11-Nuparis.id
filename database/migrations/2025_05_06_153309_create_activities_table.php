<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('activity_title');
            $table->string('activity_slug')->unique();
            $table->text('activity_description');
            $table->string('activity_image');
            $table->string('activity_category_uuid')->default(1);
            $table->string('activity_location');
            $table->timestamp('activity_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
