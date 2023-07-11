<?php

namespace Database\Seeders;

use App\Models\Permission;
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
        $user_groups = [
            [
                'name' => 'admin',
                'department' => 'ICT',
                'description' => 'ICT department',
            ],
            [
                'name' => 'exhibit',
                'department' => 'AVLCI',
                'description' => 'AVLCI',
            ],
            [
                'name' => 'employees',
                'department' => 'AVLCI',
                'description' => 'AVLCI',
            ]
        ];

        $admin_permissions = Permission::all();
        $exhibit_permissions = Permission::where('module', 'members')
            ->orWhere('module', 'employees')
            ->orWhere('module', 'assigns')
            ->orWhere('module', 'invites')->get();
        $employee_permissions = Permission::where('module', 'members')->get();

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

                    //invites permission
                    UserGroupPermission::create([
                        'user_group_id' => $usergroup->id,
                        'permission_id' => $admin_permissions->where('module', 'invites')->where('type', 'read')->first()->id
                    ]);

                    UserGroupPermission::create([
                        'user_group_id' => $usergroup->id,
                        'permission_id' => $admin_permissions->where('module', 'invites')->where('type', 'update')->first()->id
                    ]);
                }
            }
        }
    }
}
