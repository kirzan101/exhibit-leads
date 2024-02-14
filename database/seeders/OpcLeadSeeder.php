<?php

namespace Database\Seeders;

use App\Models\OpcLead;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OpcLeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $opc_leads = [
            [
                'first_name' => 'John',
                'middle_name' => 'Test',
                'last_name' => 'Smith',
                'companion_first_name' => 'Jane',
                'companion_middle_name' => 'Test',
                'companion_last_name' => 'Doe',
                'address' => 'Pasig City',
                'hotel' => 'Astoria Plaza',
                'mobile_number' => '09777',
                'occupation' => 'Tester',
                'age' => '26',
                'source' => 'ALS',
                'civil_status' => 'Single',
            ],
            [
                'first_name' => 'Finn',
                'middle_name' => 'Rest',
                'last_name' => 'Haste',
                'companion_first_name' => 'Tania',
                'companion_middle_name' => 'Ford',
                'companion_last_name' => 'Mints',
                'address' => 'Naga City',
                'hotel' => 'Astoria Plaza',
                'mobile_number' => '09799',
                'occupation' => 'Software Engineer',
                'age' => '28',
                'source' => 'ALD',
                'civil_status' => 'Single',
            ]
        ];

        foreach($opc_leads as $opc_lead) {
            OpcLead::create($opc_lead);
        }
    }
}
