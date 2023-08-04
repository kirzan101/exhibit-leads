<?php

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            [
                'name' => 'LSR',
            ],
            [
                'name' => 'ALM',
            ],
            [
                'name' => 'PRJ',
            ],
            [
                'name' => 'ROI',
            ],
            [
                'name' => 'NMB',
            ],
            [
                'name' => 'BROI',
            ],
            [
                'name' => 'BNMB',
            ]
        ];

        foreach($sources as $source) {
            Source::create($source);
        }
    }
}
