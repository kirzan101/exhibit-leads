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
            'leads', // leads crud
            'employees', // employees crud
            'assigns', // action to assign leads to an employee/booker
            'usergroups', // user group crud
            'confirms', // action to confirm leads
            'venues', // venue crud
            'sources', // source crud
            'assign-exhibitors', // action to assign leads to an exhibitor
            'surveys',
            'rois',
            'exhibits',
            'status',
            'activity-logs'
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
