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
        Schema::create('infos', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('no_whatsapp')->nullable();
            $table->text('address');
            $table->string('email');
            $table->string('instagram');
            $table->string('youtube');
            $table->string('logo');
            $table->string('meta_domain');
            $table->string('meta_title');
            $table->text('meta_desc');
            $table->string('meta_keywords');
            $table->string('meta_image');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infos');
    }
};
