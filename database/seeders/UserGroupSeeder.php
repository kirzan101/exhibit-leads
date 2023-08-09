<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Property;
use App\Models\UserGroup;
use App\Models\UserGroupPermission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $property = Property::all()->first();
        $user_groups = [
            [
                'name' => 'admin',
                'department' => 'ICT',
                'description' => 'ICT department',
                'property_id' => $property->id,
            ],
            [
                'name' => 'exhibit',
                'department' => 'AVLCI',
                'description' => 'AVLCI',
                'property_id' => $property->id,
            ],
            [
                'name' => 'employees',
                'department' => 'AVLCI',
                'description' => 'AVLCI',
                'property_id' => $property->id,
            ],
            [
                'name' => 'confirmers',
                'department' => 'AVLCI',
                'description' => 'AVLCI',
                'property_id' => $property->id,
            ],
            [
                'name' => 'exhibit-admin',
                'department' => 'AVLCI',
                'description' => 'AVLCI',
                'property_id' => $property->id,
            ]
        ];

        $admin_permissions = Permission::all();
        $exhibit_permissions = Permission::where('module', 'leads')
            ->orWhere('module', 'employees')
            ->orWhere('module', 'assigns')
            ->orWhere('module', 'confirms')
            ->orWhere('module', 'sources')
            ->orWhere('module', 'venues')
            ->get();
        $employee_permissions = Permission::where('module', 'leads')->get();
        $confirmer_permissions = Permission::where('module', 'confirms')->get();
        $exhibit_admin_permissions = Permission::where('module', 'leads')
            ->orWhere('module', 'employees')
            ->orWhere('module', 'assign-exhibitors')
            ->orWhere('module', 'sources')
            ->orWhere('module', 'venues')
            ->get();

        if ($admin_permissions->count() > 0) {
            foreach ($user_groups as $user_group) {
                $usergroup = UserGroup::create($user_group);

                if ($usergroup->name == 'admin') {
                    foreach ($admin_permissions as $permission) {
                        UserGroupPermission::create([
                            'user_group_id' => $usergroup->id,
                            'permission_id' => $permission->id
                        ]);
                    }
                } else if ($usergroup->name == 'exhibit') {
                    foreach ($exhibit_permissions as $permission) {
                        UserGroupPermission::create([
                            'user_group_id' => $usergroup->id,
                            'permission_id' => $permission->id
                        ]);
                    }
                } else if ($usergroup->name == 'exhibit-admin') {
                    foreach ($exhibit_admin_permissions as $permission) {
                        UserGroupPermission::create([
                            'user_group_id' => $usergroup->id,
                            'permission_id' => $permission->id
                        ]);
                    }
                } else if ($usergroup->name == 'employees') {
                    foreach ($employee_permissions as $permission) {
                        UserGroupPermission::create([
                            'user_group_id' => $usergroup->id,
                            'permission_id' => $permission->id
                        ]);
                    }

                    //add assign permission
                    UserGroupPermission::create([
                        'user_group_id' => $usergroup->id,
                        'permission_id' => $admin_permissions->where('module', 'assigns')->where('type', 'read')->first()->id
                    ]);

                    UserGroupPermission::create([
                        'user_group_id' => $usergroup->id,
                        'permission_id' => $admin_permissions->where('module', 'assigns')->where('type', 'update')->first()->id
                    ]);

                    //confirms permission
                    UserGroupPermission::create([
                        'user_group_id' => $usergroup->id,
                        'permission_id' => $admin_permissions->where('module', 'confirms')->where('type', 'read')->first()->id
                    ]);

                    UserGroupPermission::create([
                        'user_group_id' => $usergroup->id,
                        'permission_id' => $admin_permissions->where('module', 'confirms')->where('type', 'update')->first()->id
                    ]);
                } else if ($usergroup->name == 'confirmers') {
                    foreach ($confirmer_permissions as $permission) {
                        UserGroupPermission::create([
                            'user_group_id' => $usergroup->id,
                            'permission_id' => $permission->id
                        ]);
                    }

                    //confirms/For Confirmation permission
                    UserGroupPermission::create([
                        'user_group_id' => $usergroup->id,
                        'permission_id' => $admin_permissions->where('module', 'confirms')->where('type', 'read')->first()->id
                    ]);

                    UserGroupPermission::create([
                        'user_group_id' => $usergroup->id,
                        'permission_id' => $admin_permissions->where('module', 'confirms')->where('type', 'update')->first()->id
                    ]);
                }
            }
        }
    }
}
