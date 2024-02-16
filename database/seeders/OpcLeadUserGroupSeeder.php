<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Property;
use App\Models\UserGroup;
use App\Models\UserGroupPermission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OpcLeadUserGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $property = Property::all()->first();
        $opc_lead_permissions = Permission::where('module', 'opc-lead')->get();

        $usergroup = UserGroup::create([
            'name' => 'opc-leads',
            'code' => 'OPC-LEADS',
            'department' => 'AVLCI',
            'description' => 'AVLCI',
            'property_id' => $property->id,
        ]);

        foreach ($opc_lead_permissions as $permission) {
            UserGroupPermission::create([
                'user_group_id' => $usergroup->id,
                'permission_id' => $permission->id
            ]);
        }
    }
}
