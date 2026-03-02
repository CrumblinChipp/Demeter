<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Campus; 

class BuildingsSeeder extends Seeder
{
    public function run(): void
    {
        // Get the campus id by name
        $campusId = Campus::first()->id;

        // Insert buildings
        DB::table('buildings')->insert([
            [
                'campus_id' => $campusId,
                'name'      => 'CICS',
            ],
            [
                'campus_id' => $campusId,
                'name'      => 'CET',
            ],
            [
                'campus_id' => $campusId,
                'name'      => 'Gymnasium',
            ],
            [
                'campus_id' => $campusId,
                'name'      => 'Registrar Office',
            ],
            [
                'campus_id' => $campusId,
                'name'      => 'CEAFA',
            ],
        ]);
    }
}
