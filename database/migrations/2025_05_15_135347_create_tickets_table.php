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
        Schema::create('tickets', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('ticket_code');
            $table->string('ticket_title');
            $table->string('ticket_whatsapp');
            $table->string('ticket_email');
            $table->text('ticket_content');
            $table->string('consultant_specialization_uuid');
            $table->string('ticket_status_uuid')->default('1768c562-62a0-41e0-8f6d-76f5ec5b5e7c');
            $table->string('ticket_document_support')->nullable();
            $table->json('ticket_progress')->nullable();
            $table->timestamps();
        });
    }

    public function mutateFormDataBeforeSave(array $data): array
    {
        // Ambil progress lama (jika ada)
        $progressNotes = $this->record->ticket_progress ?? [];

        // Kalau ada catatan baru atau file baru
        if (!empty($data['progress']) || !empty($data['progress_files'])) {
            $progressNotes[] = [
                'progress' => $data['progress'] ?? '',
                'file' => $data['progress_files'] ?? [],
                'timestamp' => now()->format('Y-m-d H:i:s'),
            ];
        }

        // Masukkan ke kolom ticket_progress
        $data['ticket_progress'] = $progressNotes;

        // Hapus field form input temporer agar tidak disimpan sebagai kolom terpisah
        unset($data['progress'], $data['progress_files']);

        return $data;
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
