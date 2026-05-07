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

        $wasteTypes = [
            'biodegradable' => 'BIO',
            'recyclable'    => 'REC',
            'residual'      => 'RES',
            'infectious'    => 'INF',
        ];

        $buildings = DB::table('buildings')
            ->where('campus_id', $campusId)
            ->get();

        if ($buildings->isEmpty()) {
            $this->command->warn("No buildings found for Campus ID {$campusId}. Check your buildings table!");
            return;
        }

        $deviceCounter = 1;

        foreach ($buildings as $building) {

            foreach ($wasteTypes as $type => $shortType) {

                $cleanBuildingName = strtoupper(str_replace(' ', '_', $building->name));

                $binName = "{$cleanBuildingName}_{$type}";

                $percentage = rand(0, 100);

                $maxCapacity = 15.0;

                $calculatedWeight = round(($percentage / 100) * $maxCapacity, 2);

                // Example:
                // BIN-ENG-RES-001
                $deviceKey = sprintf(
                    'BIN-%s-%s-%03d',
                    strtoupper(substr($cleanBuildingName, 0, 3)),
                    $shortType,
                    $deviceCounter
                );

                DB::table('smart_bins')->insert([
                    'building_id'    => $building->id,
                    'name'           => $binName,
                    'device_key'     => $deviceKey,
                    'waste_type'     => $type,
                    'status'         => $percentage,
                    'current_weight' => $calculatedWeight,
                    'capacity'       => $maxCapacity,
                    'installed_at'   => now(),
                    'is_registered'  => true,
                ]); 

                $deviceCounter++;
            }
        }

        $this->command->info(
            "Smart bins created successfully for {$buildings->count()} buildings."
        );
    }
}