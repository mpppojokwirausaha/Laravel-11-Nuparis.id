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
        Schema::create('news', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('news_fullname');
            $table->string('news_slug')->unique();
            $table->string('news_url');
            $table->string('news_image');
            $table->string('news_avatar')->nullable();
            $table->string('news_content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sosial_media');
    }
};
