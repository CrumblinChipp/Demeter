<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();

            // FK → campuses table
            $table->foreignId('campus_id')->constrained()->onDelete('cascade');

            $table->string('name');
            $table->float('map_x_percent')->nullable();
            $table->float('map_y_percent')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
