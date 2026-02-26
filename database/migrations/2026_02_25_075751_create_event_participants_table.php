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
        Schema::create('event_participants', function (Blueprint $table) {
            $table->uuid('uuid')->primary();

            $table->string('participant_name');
            $table->string('participant_nib')->nullable();
            $table->text('participant_address')->nullable();
            $table->string('participant_email');
            $table->string('participant_no_wa');

            $table->string('ticket_code')->unique();
            $table->timestamp('checked_in_at')->nullable();

            $table->timestamps();

            $table->foreignUuid('event_uuid')
                ->constrained('events', 'uuid')
                ->cascadeOnDelete();

            $table->foreignUuid('order_uuid')
                ->constrained('orders', 'uuid')
                ->cascadeOnDelete();

            $table->index('event_uuid');
            $table->index('order_uuid');
            $table->index('participant_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_participants');
    }
};
