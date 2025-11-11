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
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->foreignId('cleaner_id')->nullable()->constrained('cleaners')->nullOnDelete();
            $table->string('subject');
            $table->text('message');
            $table->string('status')->default('open'); // open, pending, resolved, closed
            $table->string('priority')->default('medium'); // low, medium, high
            $table->timestamp('closed_at')->nullable();
            $table->index(['status', 'priority']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
