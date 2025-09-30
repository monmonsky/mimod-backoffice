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
        // Get roles
        $superAdminRole = DB::table('roles')->where('name', 'super_admin')->first();
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        $staffRole = DB::table('roles')->where('name', 'staff')->first();
        $customerRole = DB::table('roles')->where('name', 'customer')->first();

        // Get all permissions
        $allPermissions = DB::table('permissions')->get();

        $rolePermissions = [];

        // Super Admin - All permissions
        foreach ($allPermissions as $permission) {
            $rolePermissions[] = [
                'role_id' => $superAdminRole->id,
                'permission_id' => $permission->id,
                'granted_by' => null,
                'granted_at' => now(),
            ];
        }

        // Admin - Most permissions except system settings
        $adminPermissions = $allPermissions->filter(function ($permission) {
            return $permission->module !== 'setting' || $permission->action === 'read';
        });

        foreach ($adminPermissions as $permission) {
            $rolePermissions[] = [
                'role_id' => $adminRole->id,
                'permission_id' => $permission->id,
                'granted_by' => null,
                'granted_at' => now(),
            ];
        }

        // Staff - Limited permissions
        $staffModules = ['product', 'category', 'order', 'customer'];
        $staffActions = ['create', 'read', 'update'];

        $staffPermissions = $allPermissions->filter(function ($permission) use ($staffModules, $staffActions) {
            return in_array($permission->module, $staffModules) && in_array($permission->action, $staffActions);
        });

        foreach ($staffPermissions as $permission) {
            $rolePermissions[] = [
                'role_id' => $staffRole->id,
                'permission_id' => $permission->id,
                'granted_by' => null,
                'granted_at' => now(),
            ];
        }

        // Customer - Very limited permissions
        $customerPermissions = $allPermissions->filter(function ($permission) {
            return ($permission->module === 'order' && $permission->action === 'read') ||
                ($permission->module === 'product' && $permission->action === 'read');
        });

        foreach ($customerPermissions as $permission) {
            $rolePermissions[] = [
                'role_id' => $customerRole->id,
                'permission_id' => $permission->id,
                'granted_by' => null,
                'granted_at' => now(),
            ];
        }

        DB::table('role_permissions')->insert($rolePermissions);
    }
}
