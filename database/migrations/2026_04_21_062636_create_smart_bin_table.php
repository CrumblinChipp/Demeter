<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('smart_bins', function (Blueprint $table) {
            $table->id('bin_id');

            $table->foreignId('building_id')
                  ->constrained('buildings')
                  ->onDelete('cascade');

            $table->string('name');

            $table->integer('status')->default('0');

            $table->float('current_weight')->default(0.0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smart_bins');
    }
};