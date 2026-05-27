<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_job_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('job_class');
            $table->string('queue')->nullable();
            $table->string('status', 50)->nullable();
            $table->integer('execution_time_ms')->nullable();
            $table->integer('memory_usage_mb')->nullable();
            $table->text('exception_message')->nullable();
            $table->timestamps();

            // Index untuk mempercepat query
            $table->index('status');
            $table->index('queue');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_job_metrics');
    }
};
