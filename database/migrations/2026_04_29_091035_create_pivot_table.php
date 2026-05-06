<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bin_pivot', function (Blueprint $table) {
            $table->id();

            $table->foreignId('waste_entry_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('bin_id')
                ->constrained('smart_bins')
                ->cascadeOnDelete();

                $table->float('weight');

            $table->date('entry_date');
            
            $table->unique(['waste_entry_id', 'bin_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bin_waste_entries');
    }
};