<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('smart_bins', function (Blueprint $table) {
            $table->float('capacity')->default(0)->after('current_weight');
            $table->timestamp('installed_at')->nullable()->after('capacity');
        });
    }

    public function down(): void
    {
        Schema::table('smart_bins', function (Blueprint $table) {
            $table->dropColumn(['capacity', 'installed_at']);
        });
    }
};
