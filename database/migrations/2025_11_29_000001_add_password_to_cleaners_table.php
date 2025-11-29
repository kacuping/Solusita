<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('cleaners') && ! Schema::hasColumn('cleaners', 'password')) {
            Schema::table('cleaners', function (Blueprint $table) {
                $table->string('password')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('cleaners') && Schema::hasColumn('cleaners', 'password')) {
            Schema::table('cleaners', function (Blueprint $table) {
                $table->dropColumn('password');
            });
        }
    }
};

