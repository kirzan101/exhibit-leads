<?php

namespace Database\Seeders;

use App\Models\Venue;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VenueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $venues = [
            [
                'name' => '3502',
                'code' => '3502',
            ],
            [
                'name' => 'Function room Plaza',
                'code' => 'FRP',
            ],
            [
                'name' => 'Palawan',
                'code' => 'PW',
            ],
            [
                'name' => 'Potters',
                'code' => 'P',
            ],
            [
                'name' => 'Bora Leads',
                'code' => 'BL',
            ],
        ];

        foreach ($venues as $venue) {
            Venue::create($venue);
        }
    }
}
