<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\WasteEntry;
use App\Models\Bin;
use Illuminate\Support\Facades\DB;

class BinPivotsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pivot')->truncate();

        $entries = WasteEntry::all();

        foreach ($entries as $entry) {

            $bins = Bin::where('building_id', $entry->building_id)->get();

            // Group bins by waste type
            $binsByType = $bins->groupBy('waste_type');
            

            foreach ($binsByType as $type => $binsGroup) {

                // Get total weight from waste_entries
                $totalWeight = match ($type) {
                    'residual' => $entry->residual_kg,
                    'recyclable' => $entry->recyclable_kg,
                    'biodegradable' => $entry->biodegradable_kg,
                    'infectious' => $entry->infectious_kg,
                    default => 0,
                };

                $binCount = $binsGroup->count();

                if ($binCount === 0) continue;

                // Divide evenly
                $weightPerBin = $totalWeight / $binCount;
                

                foreach ($binsGroup as $bin) {
                    DB::table('pivot')->insert([
                        'waste_entry_id' => $entry->id,
                        'bin_id' => $bin->bin_id,
                        'weight' => $weightPerBin,
                        'entry_date' => $entry->date,
                    ]);
                }
            }
        }
    }
}