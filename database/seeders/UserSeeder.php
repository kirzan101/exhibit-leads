<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'username' => 'admin',
                'email' => 'admin@astoria.com.ph',
                'password' => bcrypt('admin')
            ],
            [
                'username' => 'christian',
                'email' => 'christian.escamilla@astoria.com.ph',
                'password' => bcrypt('q')
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
