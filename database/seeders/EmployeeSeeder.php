<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
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
                'property' => 'Astoria Plaza',
                'user_id' => 1
            ],
            [
                'first_name' => 'Christian',
                'last_name' => 'Escamilla',
                'position' => 'Administrator',
                'property' => 'Astoria Plaza',
                'user_id' => 1
            ]
        ];

        $users = [
            [
                'username' => 'admin',
                'email' => 'admin@astoria.com.ph',
                'password' => bcrypt('q')
            ],
            [
                'username' => 'cescamilla',
                'email' => 'christian.escamilla@astoria.com.ph',
                'password' => bcrypt('q')
            ]
        ];

        foreach ($employees as $key=>$employee) {
            $user = User::create($users[$key]);

            $employee['user_id'] = $user->id;
            Employee::create($employee);
        }
    }
}
