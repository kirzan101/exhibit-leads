<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OpcLeadPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'create',
            'update',
            'delete',
            'read',
        ];

        foreach ($types as $type) {
            Permission::create([
                'module' => 'opc-leads',
                'type' => $type
            ]);
        }
    }
}
