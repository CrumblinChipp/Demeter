<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
public function up(): void
    {
        // 1. Delete all existing records so we have a clean slate
        DB::table('smart_bins')->delete();

        // Schema::table('smart_bins', function (Blueprint $table) {
            // 2. Drop the old string column
         //   $table->dropColumn('status');
        //});

        //Schema::table('smart_bins', function (Blueprint $table) {
            // 3. Add the new integer column
        //    $table->integer('status')->default(0);
        //});
    }

    public function down(): void
    {
        Schema::table('smart_bins', function (Blueprint $table) {
            $table->string('status')->default('Empty')->change();
        });
    }
};