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
        Schema::table('cleaners', function (Blueprint $table) {
            // Tambahkan kolom baru sesuai kebutuhan CRUD Petugas
            if (!Schema::hasColumn('cleaners', 'full_name')) {
                $table->string('full_name')->nullable()->after('id');
            }
            if (!Schema::hasColumn('cleaners', 'address')) {
                $table->string('address')->nullable()->after('email');
            }
            if (!Schema::hasColumn('cleaners', 'birth_place')) {
                $table->string('birth_place')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('cleaners', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('birth_place');
            }
            if (!Schema::hasColumn('cleaners', 'bank_account_number')) {
                $table->string('bank_account_number')->nullable()->after('rating');
            }
            if (!Schema::hasColumn('cleaners', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('bank_account_number');
            }
            if (!Schema::hasColumn('cleaners', 'bank_account_name')) {
                $table->string('bank_account_name')->nullable()->after('bank_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cleaners', function (Blueprint $table) {
            // Hapus kolom yang ditambahkan
            if (Schema::hasColumn('cleaners', 'full_name')) {
                $table->dropColumn('full_name');
            }
            if (Schema::hasColumn('cleaners', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('cleaners', 'birth_place')) {
                $table->dropColumn('birth_place');
            }
            if (Schema::hasColumn('cleaners', 'birth_date')) {
                $table->dropColumn('birth_date');
            }
            if (Schema::hasColumn('cleaners', 'bank_account_number')) {
                $table->dropColumn('bank_account_number');
            }
            if (Schema::hasColumn('cleaners', 'bank_name')) {
                $table->dropColumn('bank_name');
            }
            if (Schema::hasColumn('cleaners', 'bank_account_name')) {
                $table->dropColumn('bank_account_name');
            }
        });
    }
};

