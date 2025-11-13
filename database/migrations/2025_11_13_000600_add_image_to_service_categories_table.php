<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('service_categories')) {
            Schema::table('service_categories', function (Blueprint $table) {
                if (! Schema::hasColumn('service_categories', 'image')) {
                    $table->string('image')->nullable()->after('icon');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('service_categories')) {
            Schema::table('service_categories', function (Blueprint $table) {
                if (Schema::hasColumn('service_categories', 'image')) {
                    $table->dropColumn('image');
                }
            });
        }
    }
};

