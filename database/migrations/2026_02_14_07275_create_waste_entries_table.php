<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waste_entries', function (Blueprint $table) {
            $table->id();

            // date of collection
            $table->date('date');

            // Building where waste was collected
            $table->foreignId('building_id')->constrained()->onDelete('cascade');

            // User who submitted the entry
            $table->unsignedBigInteger('user_id');

            // Waste amounts (kg)
            $table->integer('residual_kg')->default(0);
            $table->integer('recyclable_kg')->default(0);
            $table->integer('biodegradable_kg')->default(0);
            $table->integer('infectious_kg')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waste_entries');
    }
};
