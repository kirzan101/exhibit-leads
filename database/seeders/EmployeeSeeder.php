<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\EmployeeVenue;
use App\Models\User;
use App\Models\UserGroup;
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
                'property_id' => 1,
                'user_id' => 1,
                'user_group_id' => 1
            ],
            [
                'first_name' => 'Christian',
                'last_name' => 'Escamilla',
                'position' => 'Administrator',
                'property_id' => 1,
                'user_id' => 1,
                'user_group_id' => 1
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

        $usergroup = UserGroup::where('name', 'admin')->first(); //admin

        foreach ($employees as $key => $employee) {
            $user = User::create($users[$key]);

            $employee['user_id'] = $user->id;
            $employee['user_group_id'] = $usergroup->id;
            Employee::create($employee);
        }

        // create employee account
        $user_emp = User::create([
            'username' => 'employee',
            'email' => 'employee@astoria.com.ph',
            'password' => bcrypt('q')
        ]);

        Employee::create([
            'first_name' => 'Employee',
            'last_name' => 'Account',
            'position' => 'Employee',
            'property_id' => 1,
            'user_id' => $user_emp->id,
            'user_group_id' => UserGroup::where('name', 'employees')->first()->id,
        ]);

        // create exhibitor account
        $user_exhibitor = User::create([
            'username' => 'exhibitor',
            'email' => 'exhibitor@astoria.com.ph',
            'password' => bcrypt('q')
        ]);

        Employee::create([
            'first_name' => 'Exhibitor',
            'last_name' => 'Account',
            'position' => 'Exhibitor',
            'property_id' => 1,
            'user_id' => $user_exhibitor->id,
            'user_group_id' => UserGroup::where('name', 'exhibit')->first()->id
        ]);

        // create confirmer account
        $user_confirmer = User::create([
            'username' => 'confirmer',
            'email' => 'confirmer@astoria.com.ph',
            'password' => bcrypt('q')
        ]);

        Employee::create([
            'first_name' => 'Confirmer',
            'last_name' => 'Account',
            'position' => 'Confirmer',
            'property_id' => 1,
            'user_id' => $user_confirmer->id,
            'user_group_id' => UserGroup::where('name', 'confirmers')->first()->id
        ]);

        // create exhibitor account
        $user_exhibitor_admin = User::create([
            'username' => 'exhibitor-admin',
            'email' => 'exhibitor-admin@astoria.com.ph',
            'password' => bcrypt('q')
        ]);

        Employee::create([
            'first_name' => 'Exhibitor Admin',
            'last_name' => 'Account',
            'position' => 'Exhibitor Admin',
            'property_id' => 1,
            'user_id' => $user_exhibitor_admin->id,
            'user_group_id' => UserGroup::where('name', 'exhibit-admin')->first()->id
        ]);

        //create default venue per employee
        $employees = Employee::all();
        $user_group = UserGroup::where('name', 'employees')->first();
        foreach($employees as $employee) {
            EmployeeVenue::create([
                'employee_id' => $employee->id,
                'venue_id' => 1
            ]);

            // set default exhibitor
            // id 4: Exhibitor account
            if($employee->user_group_id == $user_group->id) {
                $employee->update([
                    'exhibitor_id' => 4
                ]);
            }
        }
    }
}
