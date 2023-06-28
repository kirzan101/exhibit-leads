<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            [
                'first_name' => 'Admin',
                'last_name' => 'Account',
                'position' => 'Administrator',
                'property' => 'Astoria Plaza'
            ],
            [
                'first_name' => 'Christian',
                'last_name' => 'Escamilla',
                'position' => 'Administrator',
                'property' => 'Astoria Plaza'
            ]
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }
    }
}
