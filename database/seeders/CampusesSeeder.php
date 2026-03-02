<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampusesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('campuses')->insert([
            [
                'name'      => 'Alangilan',
            ],
            [
                'name'      => 'Pablo Borbon',
            ],

        ]);
    }
}
