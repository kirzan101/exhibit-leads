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
                'name' => 'ROI-KR'
            ],
            [
                'name' => 'NMB-KR'
            ],
            [
                'name' => 'ROI-Dodie'
            ],
            [
                'name' => 'NMB-Dodie'
            ],
            [
                'name' => 'ROI-Elson'
            ],
            [
                'name' => 'NMB-Elson'
            ],
            [
                'name' => 'ROI-Oliver'
            ],
            [
                'name' => 'NMB-Oliver'
            ],
            [
                'name' => 'BROIJ'
            ],
            [
                'name' => 'BNMBJ'
            ],
            [
                'name' => 'BROII'
            ],
            [
                'name' => 'BNMBI'
            ],
            [
                'name' => 'BROIM'
            ],
            [
                'name' => 'BNMBM'
            ],
            [
                'name' => 'BROIV'
            ],
            [
                'name' => 'BNMBV'
            ],
        ];

        foreach($sources as $source) {
            Source::create($source);
        }
    }
}
