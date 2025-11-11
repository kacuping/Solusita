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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('method')->default('cash'); // cash, transfer, ewallet
            $table->string('status')->default('pending'); // pending, paid, failed, refunded
            $table->string('transaction_ref')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->index(['status', 'method']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
