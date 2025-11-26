<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            if (! Schema::hasColumn('promotions', 'popup_enabled')) {
                $table->boolean('popup_enabled')->default(false)->after('image_path');
            }
            if (! Schema::hasColumn('promotions', 'popup_max_per_day')) {
                $table->unsignedInteger('popup_max_per_day')->default(1)->after('popup_enabled');
            }
            if (! Schema::hasColumn('promotions', 'popup_hours')) {
                $table->json('popup_hours')->nullable()->after('popup_max_per_day');
            }
        });
    }

    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            if (Schema::hasColumn('promotions', 'popup_hours')) {
                $table->dropColumn('popup_hours');
            }
            if (Schema::hasColumn('promotions', 'popup_max_per_day')) {
                $table->dropColumn('popup_max_per_day');
            }
            if (Schema::hasColumn('promotions', 'popup_enabled')) {
                $table->dropColumn('popup_enabled');
            }
        });
    }
};

