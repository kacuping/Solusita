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
        if (Schema::hasTable('cleaners')) {
            Schema::table('cleaners', function (Blueprint $table) {
                if (!Schema::hasColumn('cleaners', 'status')) {
                    $table->string('status')->default('pending');
                }
                if (!Schema::hasColumn('cleaners', 'active')) {
                    $table->boolean('active')->default(false);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('cleaners')) {
            Schema::table('cleaners', function (Blueprint $table) {
                if (Schema::hasColumn('cleaners', 'status')) {
                    $table->dropColumn('status');
                }
                if (Schema::hasColumn('cleaners', 'active')) {
                    $table->dropColumn('active');
                }
            });
        }
    }
};

