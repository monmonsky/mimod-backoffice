<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('role_modules')->truncate();

        // Get roles
        $superAdminRole = DB::table('roles')->where('name', 'super_admin')->first();
        $adminRole = DB::table('roles')->where('name', 'admin')->first();

        if (!$superAdminRole || !$adminRole) {
            $this->command->warn('Roles not found');
            return;
        }

        // Get all modules
        $allModules = DB::table('modules')->get();

        $roleModules = [];

        foreach ($allModules as $module) {
            // Super Admin - Full access to all modules
            $roleModules[] = [
                'role_id' => $superAdminRole->id,
                'module_id' => $module->id,
                'can_view' => true,
                'can_create' => true,
                'can_update' => true,
                'can_delete' => true,
                'can_export' => true,
                'custom_permissions' => null,
                'granted_by' => null,
                'granted_at' => now(),
            ];

            // Admin - View only for all modules
            $roleModules[] = [
                'role_id' => $adminRole->id,
                'module_id' => $module->id,
                'can_view' => true,
                'can_create' => false,
                'can_update' => false,
                'can_delete' => false,
                'can_export' => false,
                'custom_permissions' => null,
                'granted_by' => null,
                'granted_at' => now(),
            ];
        }

        if (!empty($roleModules)) {
            DB::table('role_modules')->insert($roleModules);
        }

        $this->command->info('Role modules seeded successfully.');
    }
}
