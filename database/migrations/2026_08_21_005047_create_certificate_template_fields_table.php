<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_template_fields', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('certificate_template_id')
                ->constrained('certificate_templates', 'uuid')
                ->cascadeOnDelete();

            $table->string('field_key');   // nama, keterangan, tempat, tanggal, tahun, barcode
            $table->string('label');       // label yang ditampilkan di UI marking

            $table->decimal('x', 5, 2)->default(50);  // posisi horizontal, dalam % dari lebar gambar
            $table->decimal('y', 5, 2)->default(50);  // posisi vertikal, dalam % dari tinggi gambar

            $table->unsignedInteger('font_size')->default(20); // untuk barcode dipakai sebagai lebar (px)
            $table->string('font_color')->default('#000000');

            $table->timestamps();

            $table->unique(['certificate_template_id', 'field_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_template_fields');
    }
};
