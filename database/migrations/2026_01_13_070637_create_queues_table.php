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
        Schema::create('queues', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignUlid('counter_id')->nullable()->constrained('counters')->nullOnDelete();
            $table->string('ticket_number');
            $table->integer('sequence');
            $table->enum('status', ['waiting', 'called', 'serving', 'completed', 'skipped', 'cancelled']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
