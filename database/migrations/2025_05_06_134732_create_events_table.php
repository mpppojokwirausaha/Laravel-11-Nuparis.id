<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('event_title');
            $table->string('event_slug')->unique();
            $table->text('event_description');
            $table->string('event_image');
            $table->timestamp('event_date_start');
            $table->timestamp('event_date_end');
            $table->string('event_location');
            $table->string('event_category_uuid');
            $table->string('event_price');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
