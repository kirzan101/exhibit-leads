<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            'leads',
            'employees',
            'assigns',
            'usergroups',
            'invites',
            'confirms',
            'venues',
            'sources'
        ];

        $types = [
            'create',
            'update',
            'delete',
            'read',
        ];

        foreach ($modules as $module) {
            
            foreach($types as $type) {
                Permission::create([
                    'module' => $module,
                    'type' => $type
                ]);
            }
        }
    }
}
