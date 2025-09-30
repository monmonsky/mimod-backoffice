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
        // Get roles
        $superAdminRole = DB::table('roles')->where('name', 'super_admin')->first();
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        $staffRole = DB::table('roles')->where('name', 'staff')->first();
        $customerRole = DB::table('roles')->where('name', 'customer')->first();

        // Get all modules
        $allModules = DB::table('modules')->get();

        $roleModules = [];

        // Super Admin - Full access to all modules
        foreach ($allModules as $module) {
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
        }

        // Admin - Full access except settings
        foreach ($allModules as $module) {
            $isSettings = $module->name === 'settings';

            $roleModules[] = [
                'role_id' => $adminRole->id,
                'module_id' => $module->id,
                'can_view' => true,
                'can_create' => !$isSettings,
                'can_update' => !$isSettings,
                'can_delete' => !$isSettings,
                'can_export' => true,
                'custom_permissions' => null,
                'granted_by' => null,
                'granted_at' => now(),
            ];
        }

        // Staff - Limited access
        $staffModuleNames = ['dashboard', 'products', 'categories', 'orders', 'customers', 'product_management', 'order_management'];
        $staffModules = $allModules->whereIn('name', $staffModuleNames);

        foreach ($staffModules as $module) {
            $isManagementModule = in_array($module->name, ['product_management', 'order_management']);

            $roleModules[] = [
                'role_id' => $staffRole->id,
                'module_id' => $module->id,
                'can_view' => true,
                'can_create' => !$isManagementModule,
                'can_update' => !$isManagementModule,
                'can_delete' => false,
                'can_export' => false,
                'custom_permissions' => null,
                'granted_by' => null,
                'granted_at' => now(),
            ];
        }

        // Customer - Very limited access
        $customerModuleNames = ['dashboard', 'products', 'orders'];
        $customerModules = $allModules->whereIn('name', $customerModuleNames);

        foreach ($customerModules as $module) {
            $roleModules[] = [
                'role_id' => $customerRole->id,
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

        DB::table('role_modules')->insert($roleModules);
    }
}
