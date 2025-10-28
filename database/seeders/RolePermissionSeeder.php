<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('role_permissions')->truncate();

        // Get roles
        $superAdminRole = DB::table('roles')->where('name', 'super_admin')->first();
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        $staffRole = DB::table('roles')->where('name', 'staff')->first();

        if (!$superAdminRole || !$adminRole || !$staffRole) {
            $this->command->warn('Roles not found');
            return;
        }

        // Get all permissions
        $allPermissions = DB::table('permissions')->get();

        $rolePermissions = [];

        // Super Admin - All permissions (68 permissions)
        foreach ($allPermissions as $permission) {
            $rolePermissions[] = [
                'role_id' => $superAdminRole->id,
                'permission_id' => $permission->id,
            ];
        }

        // Admin - All permissions (68 permissions)
        foreach ($allPermissions as $permission) {
            $rolePermissions[] = [
                'role_id' => $adminRole->id,
                'permission_id' => $permission->id,
            ];
        }

        // Staff - Limited permissions (Catalog + Dashboard only)
        $staffPermissionNames = [
            'dashboard.view',
            // Products
            'catalog.products.view',
            'catalog.products.create',
            'catalog.products.update',
            // Brands
            'catalog.brands.view',
            'catalog.brands.create',
            // Categories
            'catalog.categories.view',
            'catalog.categories.create',
        ];

        $staffPermissions = $allPermissions->filter(function ($permission) use ($staffPermissionNames) {
            return in_array($permission->name, $staffPermissionNames);
        });

        foreach ($staffPermissions as $permission) {
            $rolePermissions[] = [
                'role_id' => $staffRole->id,
                'permission_id' => $permission->id,
            ];
        }

        if (!empty($rolePermissions)) {
            DB::table('role_permissions')->insert($rolePermissions);
        }

        $this->command->info('Role permissions seeded successfully.');
        $this->command->info('Super Admin: ' . $allPermissions->count() . ' permissions');
        $this->command->info('Admin: ' . $allPermissions->count() . ' permissions');
        $this->command->info('Staff: ' . $staffPermissions->count() . ' permissions');
    }
}
