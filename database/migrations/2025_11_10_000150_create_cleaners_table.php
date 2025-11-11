<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('cleaners')) {
            Schema::create('cleaners', function (Blueprint $table) {
                $table->id();
                $table->string('full_name');
                $table->string('address')->nullable();
                $table->string('phone')->nullable();
                $table->string('birth_place')->nullable();
                $table->date('birth_date')->nullable();
                $table->string('bank_account_number')->nullable();
                $table->string('bank_name')->nullable();
                $table->string('bank_account_name')->nullable();
                // Kolom untuk alur approval
                $table->string('status')->default('pending'); // pending|approved|rejected
                $table->boolean('active')->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('cleaners')) {
            Schema::dropIfExists('cleaners');
        }
    }
};
