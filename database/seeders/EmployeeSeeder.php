<?php

namespace Database\Seeders;

use App\Helpers\Helper;
use App\Models\Employee;
use App\Models\EmployeeVenue;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\Venue;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //create default users
        $accounts = [
            [
                'first_name' => 'Admin',
                'last_name' => 'Account',
                'position' => 'Admin',
                'property_id' => 1,
                'user_group' => 'admin',
                'email' => 'admin@astoria.com.ph',
                'is_password_changed' => true
            ],
            [
                'first_name' => 'Christian',
                'last_name' => 'Escamilla',
                'position' => 'Admin',
                'property_id' => 1,
                'user_group' => 'admin',
                'email' => 'christian.escamilla@astoria.com.ph',
                'is_password_changed' => true
            ],
            [
                'first_name' => 'Jose',
                'last_name' => 'Bustamante',
                'position' => 'Admin',
                'property_id' => 1,
                'user_group' => 'admin',
                'email' => 'jose.bustamante@astoria.com.ph',
                'is_password_changed' => true
            ],
            // [
            //     'first_name' => 'Exhibitor admin',
            //     'last_name' => 'Account',
            //     'position' => 'Exhibitor Admin',
            //     'property_id' => 1,
            //     'user_group' => 'exhibit-admin',
            //     'email' => 'exhibitor-admin@astoria.com.ph',
            //     'is_password_changed' => true
            // ],
            // [
            //     'first_name' => 'Exhibitor',
            //     'last_name' => 'Account',
            //     'position' => 'Exhibitor',
            //     'property_id' => 1,
            //     'user_group' => 'exhibit',
            //     'email' => 'exhibitor@astoria.com.ph',
            //     'is_password_changed' => true
            // ],
            // [
            //     'first_name' => 'ROI',
            //     'last_name' => 'Account',
            //     'position' => 'ROI',
            //     'property_id' => 1,
            //     'user_group' => 'rois',
            //     'email' => 'roi@astoria.com.ph',
            //     'is_password_changed' => true
            // ],
            // [
            //     'first_name' => 'Survey',
            //     'last_name' => 'Account',
            //     'position' => 'Survey',
            //     'property_id' => 1,
            //     'user_group' => 'surveys',
            //     'email' => 'survey@astoria.com.ph',
            //     'is_password_changed' => true
            // ],
            // [
            //     'first_name' => 'Encoder',
            //     'last_name' => 'Account',
            //     'position' => 'Encoder',
            //     'property_id' => 1,
            //     'user_group' => 'encoders',
            //     'email' => 'encoder@astoria.com.ph',
            //     'is_password_changed' => true
            // ],
            // [
            //     'first_name' => 'Confirmer1',
            //     'last_name' => 'Account',
            //     'position' => 'Confirmer',
            //     'property_id' => 1,
            //     'user_group' => 'confirmers',
            //     'email' => 'confirmer1@astoria.com.ph',
            //     'is_password_changed' => true
            // ],
            // [
            //     'first_name' => 'Confirmer2',
            //     'last_name' => 'Account',
            //     'position' => 'Confirmer',
            //     'property_id' => 1,
            //     'user_group' => 'confirmers',
            //     'email' => 'confirmer2@astoria.com.ph',
            //     'is_password_changed' => true
            // ],
            // [
            //     'first_name' => 'Booker1',
            //     'last_name' => 'Account',
            //     'position' => 'Booker',
            //     'property_id' => 1,
            //     'user_group' => 'employees',
            //     'email' => 'booker1@astoria.com.ph',
            //     'is_password_changed' => true
            // ],
            // [
            //     'first_name' => 'Booker2',
            //     'last_name' => 'Account',
            //     'position' => 'Booker',
            //     'property_id' => 1,
            //     'user_group' => 'employees',
            //     'email' => 'booker2@astoria.com.ph',
            //     'is_password_changed' => true
            // ],
            // [
            //     'first_name' => 'ROI1',
            //     'last_name' => 'Account',
            //     'position' => 'ROI',
            //     'property_id' => 1,
            //     'user_group' => 'employees',
            //     'email' => 'roi-booker1@astoria.com.ph',
            //     'is_password_changed' => true
            // ],
            // [
            //     'first_name' => 'ROI2',
            //     'last_name' => 'Account',
            //     'position' => 'ROI',
            //     'property_id' => 1,
            //     'user_group' => 'employees',
            //     'email' => 'roi-booker2@astoria.com.ph',
            //     'is_password_changed' => true
            // ],
            // [
            //     'first_name' => 'Survey1',
            //     'last_name' => 'Account',
            //     'position' => 'Survey',
            //     'property_id' => 1,
            //     'user_group' => 'employees',
            //     'email' => 'survey-booker1@astoria.com.ph',
            //     'is_password_changed' => true
            // ],
            // [
            //     'first_name' => 'Survey2',
            //     'last_name' => 'Account',
            //     'position' => 'Survey',
            //     'property_id' => 1,
            //     'user_group' => 'employees',
            //     'email' => 'survey-booker2@astoria.com.ph',
            //     'is_password_changed' => true
            // ],
        ];

        $exhibit_admin = '';
        $roi_admin = '';
        $survey_admin = '';

        foreach ($accounts as $account) {
            $email = ($account['email'] != '') ? $account['email'] : Helper::createEmail($account['first_name'], $account['last_name']);

            $user = User::create([
                'username' => Helper::username($account['first_name'], $account['last_name']),
                'email' => $email,
                'password' => bcrypt('q'),
                'is_password_changed' => $account['is_password_changed']
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

            // add confirmer venues
            $venues = Venue::all();
            if ($account['user_group'] == 'confirmers' || $account['user_group'] == 'employees') {
                foreach($venues as $venue) {
                    EmployeeVenue::create([
                        'employee_id' => $employee->id,
                        'venue_id' => $venue->id
                    ]);
                }
            }
        }
    }
}
