<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Index waste_entries for faster dashboard/data queries
        Schema::table('waste_entries', function (Blueprint $table) {
            $table->index(['building_id', 'date']);
        });

        // Index smart_bins for faster bin section queries
        Schema::table('smart_bins', function (Blueprint $table) {
            $table->index('building_id');
        });

        // Index pivot for faster collection history lookups
        Schema::table('pivot', function (Blueprint $table) {
            $table->index('bin_id');
        });

        // Index buildings for faster campus filtering
        Schema::table('buildings', function (Blueprint $table) {
            $table->index('campus_id');
        });
    }

    public function down(): void
    {
        Schema::table('waste_entries', function (Blueprint $table) {
            $table->dropIndex(['building_id', 'date']);
        });
        Schema::table('smart_bins', function (Blueprint $table) {
            $table->dropIndex(['building_id']);
        });
        Schema::table('pivot', function (Blueprint $table) {
            $table->dropIndex(['bin_id']);
        });
        Schema::table('buildings', function (Blueprint $table) {
            $table->dropIndex(['campus_id']);
        });
    }
};
