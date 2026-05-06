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
                'map'       => 'maps/i6PJC3AR3QJJgUT6XNSth4c8Mh9VxiCZsmUPa6on.png',
            ],
            [
                'name'      => 'Pablo Borbon',
                'map'       => 'maps/8zD5I5VbNInRwOrZhzohuAagJzRn5j22Qu8ZAgyU.png',
            ],

        ]);
    }
}
