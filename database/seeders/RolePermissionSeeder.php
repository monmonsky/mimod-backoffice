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

        if (!$superAdminRole || !$adminRole) {
            $this->command->warn('Roles not found');
            return;
        }

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

        // Admin - Only view permissions
        $adminPermissions = $allPermissions->filter(function ($permission) {
            return str_contains($permission->name, '.view');
        });

        foreach ($adminPermissions as $permission) {
            $rolePermissions[] = [
                'role_id' => $adminRole->id,
                'permission_id' => $permission->id,
                'granted_by' => null,
                'granted_at' => now(),
            ];
        }

        if (!empty($rolePermissions)) {
            DB::table('role_permissions')->insert($rolePermissions);
        }

        $this->command->info('Role permissions seeded successfully.');
    }
}
