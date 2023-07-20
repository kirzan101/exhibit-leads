<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $properties = [
            [
                'name' => 'Astoria Plaza'
            ],
            [
                'name' => 'Astoria Current'
            ],
            [
                'name' => 'Astoria Bohol'
            ],
            [
                'name' => 'Astoria Greenbelt'
            ],
            [
                'name' => 'Astoria Boracay'
            ],
            [
                'name' => 'Astoria Palawan'
            ],
            [
                'name' => 'Stellar Potter\'s Ridge'
            ],
        ];

        foreach($properties as $property) {
            Property::create($property);
        }
    }
}
