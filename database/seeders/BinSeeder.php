<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $campusId = 1;

        $wasteTypes = ['biodegradable', 'recyclable', 'residual', 'infectious'];

        $buildings = DB::table('buildings')->where('campus_id', $campusId)->get();

        if ($buildings->isEmpty()) {
            $this->command->warn("No buildings found for Campus ID {$campusId}. Check your buildings table!");
            return;
        }

        foreach ($buildings as $building) {
            foreach ($wasteTypes as $type) {
                $cleanBuildingName = str_replace(' ', '_', $building->name);
                $binName = "{$cleanBuildingName}_{$type}";
                $percentage = rand(0, 100);

                $maxCapacity = 15.0;

                $calculatedWeight = round(($percentage / 100) * $maxCapacity, 2);

                DB::table('smart_bins')->insert([
                    'building_id'    => $building->id,
                    'name'           => $binName,
                    'waste_type'     => $type,
                    'status'         => $percentage,
                    'current_weight' => $calculatedWeight,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        }

        $this->command->info("Smart bins created successfully for " . $buildings->count() . " buildings.");
    }
}