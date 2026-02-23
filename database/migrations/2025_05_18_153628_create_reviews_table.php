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
        Schema::create('reviews', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('review_fullname');
            $table->string('review_slug')->unique();
            $table->string('review_link');
            $table->string('review_rating');
            $table->text('review_content');
            $table->string('review_avatar');
            $table->string('review_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
