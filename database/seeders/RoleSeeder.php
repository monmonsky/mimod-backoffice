<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Administrator',
                'description' => 'Has full access to all system features and settings',
                'is_active' => true,
                'is_system' => true,
                'priority' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Has access to most system features',
                'is_active' => true,
                'is_system' => true,
                'priority' => 80,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'staff',
                'display_name' => 'Staff',
                'description' => 'Has limited access to system features',
                'is_active' => true,
                'is_system' => false,
                'priority' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'customer',
                'display_name' => 'Customer',
                'description' => 'Regular customer with basic access',
                'is_active' => true,
                'is_system' => false,
                'priority' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('roles')->insert($roles);
    }
}
