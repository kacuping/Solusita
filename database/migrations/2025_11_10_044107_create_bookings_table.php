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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->restrictOnDelete();
            $table->foreignId('cleaner_id')->nullable()->constrained('cleaners')->nullOnDelete();
            $table->dateTime('scheduled_at');
            $table->string('status')->default('pending'); // pending, confirmed, in_progress, completed, cancelled
            $table->text('address');
            $table->text('notes')->nullable();
            $table->unsignedInteger('duration_minutes');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->string('payment_status')->default('unpaid'); // unpaid, paid, refunded, failed
            $table->string('promotion_code')->nullable();
            $table->index(['customer_id', 'service_id']);
            $table->index('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
