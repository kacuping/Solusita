<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'dp_status')) {
                $table->string('dp_status')->default('none')->after('payment_status'); // none, unpaid, paid
                $table->index('dp_status');
            }
            if (!Schema::hasColumn('bookings', 'dp_proof')) {
                $table->string('dp_proof')->nullable()->after('dp_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'dp_proof')) {
                $table->dropColumn('dp_proof');
            }
            if (Schema::hasColumn('bookings', 'dp_status')) {
                $table->dropIndex(['dp_status']);
                $table->dropColumn('dp_status');
            }
        });
    }
};

