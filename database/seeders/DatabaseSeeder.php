<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Employee;
use App\Models\Lead;
use App\Models\Member;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            PermissionSeeder::class,
            PropertySeeder::class,
            SourceSeeder::class,
            UserGroupSeeder::class,
            VenueSeeder::class,
            EmployeeSeeder::class,
            LiveAccountSeeder::class
        ]);

        // Lead::factory(50)->create();
        
        // Employee::factory(2)->create();

    }
}
