<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('permission_groups')->truncate();

        $groups = [
            [
                'name' => 'dashboard_management',
                'display_name' => 'Dashboard Management',
                'description' => 'Permissions for dashboard access',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'access_control_management',
                'display_name' => 'Access Control Management',
                'description' => 'Permissions for user access control management',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'settings_management',
                'display_name' => 'Settings Management',
                'description' => 'Permissions for application settings management',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('permission_groups')->insert($groups);

        $this->command->info('Permission groups seeded successfully.');
    }
}
