<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WasteEntriesSeeder extends Seeder
{
    public function run(): void
    {
        $buildingIds = DB::table('buildings')->pluck('id')->toArray();
        $userIds = DB::table('users')->pluck('id')->toArray();

        $daysBack = 14; // last (decide how many days) days

        foreach ($buildingIds as $buildingId) {
            for ($i = 0; $i < $daysBack; $i++) {
                // Random chance to skip this day (e.g., 30% chance to skip)
                if (rand(1, 100) <= 15) continue;

                $date = Carbon::now()->subDays($i);

                DB::table('waste_entries')->insert([
                    'building_id'   => $buildingId,
                    'user_id'       => $userIds[array_rand($userIds)],
                    'date'          => $date,
                    'residual_kg'      => rand(20, 50),
                    'recyclable_kg'    => rand(5, 30),
                    'biodegradable_kg' => rand(5, 25),
                    'infectious_kg'    => rand(0, 10),
                ]);
            }
        }
    }
}
