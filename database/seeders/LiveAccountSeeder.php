<?php

namespace Database\Seeders;

use App\Helpers\Helper;
use App\Models\Employee;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LiveAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exhibitor_accounts = [
            // exhibit
            [
                'first_name' => 'Mimi',
                'last_name' => 'Garcia',
                'position' => 'Exhibitor',
                'property_id' => 1,
                'user_group' => 'exhibit'
            ],
            [
                'first_name' => 'Jenny',
                'last_name' => 'San Jose',
                'position' => 'Exhibitor',
                'property_id' => 1,
                'user_group' => 'exhibit'
            ],
            // exhibit admin
            [
                'first_name' => 'Cyrus',
                'last_name' => 'Dadacay',
                'position' => 'Exhibitor',
                'property_id' => 1,
                'user_group' => 'exhibit-admin'
            ],
            // encoder
            [
                'first_name' => 'Marie',
                'last_name' => 'Admana',
                'position' => 'Exhibitor',
                'property_id' => 1,
                'user_group' => 'encoders'
            ],
            // surveys
            [
                'first_name' => 'Ethelbert',
                'last_name' => 'Ravago',
                'position' => 'Survey',
                'property_id' => 1,
                'user_group' => 'surveys'
            ],
            [
                'first_name' => 'Ma. Eliza',
                'last_name' => 'Caneza',
                'position' => 'Survey',
                'property_id' => 1,
                'user_group' => 'employees'
            ],
            [
                'first_name' => 'Raquel',
                'last_name' => 'Cañada',
                'position' => 'Survey',
                'property_id' => 1,
                'user_group' => 'employees'
            ],
            [
                'first_name' => 'Reynaldo',
                'last_name' => 'Cortez',
                'position' => 'Survey',
                'property_id' => 1,
                'user_group' => 'employees'
            ],
            // roi
            [
                'first_name' => 'Leander',
                'last_name' => 'Manalo',
                'position' => 'ROI',
                'property_id' => 1,
                'user_group' => 'rois'
            ],
            [
                'first_name' => 'Jose',
                'last_name' => 'Gloria',
                'position' => 'ROI',
                'property_id' => 1,
                'user_group' => 'employees'
            ],
            [
                'first_name' => 'John Robert',
                'last_name' => 'Calderon',
                'position' => 'ROI',
                'property_id' => 1,
                'user_group' => 'employees'
            ],
            [
                'first_name' => 'Vaeda',
                'last_name' => 'Perez',
                'position' => 'ROI',
                'property_id' => 1,
                'user_group' => 'employees'
            ],
            [
                'first_name' => 'Dianne',
                'last_name' => 'David',
                'position' => 'ROI',
                'property_id' => 1,
                'user_group' => 'employees'
            ],
            // confirmers
            [
                'first_name' => 'Glenn',
                'last_name' => 'Atencio',
                'position' => 'Confirmer',
                'property_id' => 1,
                'user_group' => 'confirmers'
            ],
            [
                'first_name' => 'Arvin',
                'last_name' => 'Aguilar',
                'position' => 'Confirmer',
                'property_id' => 1,
                'user_group' => 'confirmers'
            ],
            [
                'first_name' => 'Claribel',
                'last_name' => 'Mendoza',
                'position' => 'Confirmer',
                'property_id' => 1,
                'user_group' => 'confirmers'
            ],
            [
                'first_name' => 'Lilian',
                'last_name' => 'Cardeñas',
                'position' => 'Confirmer',
                'property_id' => 1,
                'user_group' => 'confirmers'
            ],
            // employees
            [
                'first_name' => 'Erica',
                'last_name' => 'Loyola',
                'position' => 'Booker',
                'property_id' => 1,
                'user_group' => 'employees'
            ],
            [
                'first_name' => 'Joy',
                'last_name' => 'Reyes',
                'position' => 'Booker',
                'property_id' => 1,
                'user_group' => 'employees'
            ],
            [
                'first_name' => 'Jesthony',
                'last_name' => 'Delos Santos',
                'position' => 'Booker',
                'property_id' => 1,
                'user_group' => 'employees'
            ],
            [
                'first_name' => 'Rechelle Anne',
                'last_name' => 'Pajarillo',
                'position' => 'Booker',
                'property_id' => 1,
                'user_group' => 'employees'
            ],
            [
                'first_name' => 'Kennon Koi',
                'last_name' => 'Armamento',
                'position' => 'Booker',
                'property_id' => 1,
                'user_group' => 'employees'
            ],
            [
                'first_name' => 'Debralie',
                'last_name' => 'Calma',
                'position' => 'Booker',
                'property_id' => 1,
                'user_group' => 'employees'
            ],
            [
                'first_name' => 'Melissa',
                'last_name' => 'Panaligan',
                'position' => 'Booker',
                'property_id' => 1,
                'user_group' => 'employees'
            ],
            [
                'first_name' => 'Raymond',
                'last_name' => 'Borgoños',
                'position' => 'Booker',
                'property_id' => 1,
                'user_group' => 'employees'
            ],
            [
                'first_name' => 'Ronilo',
                'last_name' => 'Amparo',
                'position' => 'Booker',
                'property_id' => 1,
                'user_group' => 'employees'
            ],
            [
                'first_name' => 'Syrel',
                'last_name' => 'Zarsuela',
                'position' => 'Booker',
                'property_id' => 1,
                'user_group' => 'employees'
            ],
            [
                'first_name' => 'Rosemarie',
                'last_name' => 'Lumbo',
                'position' => 'Booker',
                'property_id' => 1,
                'user_group' => 'employees'
            ],
            [
                'first_name' => 'Cory Anne',
                'last_name' => 'Rolloque',
                'position' => 'Booker',
                'property_id' => 1,
                'user_group' => 'employees'
            ],
            [
                'first_name' => 'Armando',
                'last_name' => 'Pagaran',
                'position' => 'Booker',
                'property_id' => 1,
                'user_group' => 'employees'
            ],
            [
                'first_name' => 'Job',
                'last_name' => 'Tuale',
                'position' => 'Booker',
                'property_id' => 1,
                'user_group' => 'employees'
            ],
            [
                'first_name' => 'Clarisse Nayve',
                'last_name' => 'Gallientes',
                'position' => 'Booker',
                'property_id' => 1,
                'user_group' => 'employees'
            ],
            [
                'first_name' => 'Crisandy',
                'last_name' => 'Glorioso',
                'position' => 'Booker',
                'property_id' => 1,
                'user_group' => 'employees'
            ],
            [
                'first_name' => 'Leny',
                'last_name' => 'Tungala',
                'position' => 'Booker',
                'property_id' => 1,
                'user_group' => 'employees'
            ],
            [
                'first_name' => 'Rudolph',
                'last_name' => 'Nazareno',
                'position' => 'Booker',
                'property_id' => 1,
                'user_group' => 'employees'
            ],
        ];

        $exhibit_admin = '';
        $roi_admin = '';
        $survey_admin = '';

        // create exhibitor account
        foreach ($exhibitor_accounts as $account) {
            $user = User::create([
                'username' => Helper::username($account['first_name'], $account['last_name']),
                'email' => Helper::createEmail($account['first_name'], $account['last_name']),
                'password' => bcrypt('q')
            ]);

            $employee = Employee::create([
                'first_name' => $account['first_name'],
                'last_name' => $account['last_name'],
                'position' => $account['position'],
                'property_id' => $account['property_id'],
                'user_id' => $user->id,
                'user_group_id' => UserGroup::where('name', $account['user_group'])->first()->id
            ]);

            // get the admin Ids
            if ($account['user_group'] == 'exhibit') {
                $exhibit_admin = $employee->id;
            } else if ($account['user_group'] == 'surveys') {
                $survey_admin = $employee->id;
            } else if ($account['user_group'] == 'rois') {
                $roi_admin = $employee->id;
            }

            // update employee exhibitor id
            if ($account['position'] == 'Booker' && $account['user_group'] != 'exhibit') {
                $employee->update([
                    'exhibitor_id' => $exhibit_admin,
                    'updated_at' => now()
                ]);
            } else if ($account['position'] == 'Survey' && $account['user_group'] != 'surveys') {
                $employee->update([
                    'exhibitor_id' => $survey_admin,
                    'updated_at' => now()
                ]);
            } else if ($account['position'] == 'ROI' && $account['user_group'] != 'rois') {
                $employee->update([
                    'exhibitor_id' => $roi_admin,
                    'updated_at' => now()
                ]);
            }
        }
    }
}
