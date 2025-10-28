<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Assigns all permissions to Super Admin role
     */
    public function run(): void
    {
        // Get Super Admin role
        $superAdminRole = DB::table('roles')->where('name', 'super_admin')->first();

        if (!$superAdminRole) {
            $this->command->error('Super Admin role not found! Please run RoleSeeder first.');
            return;
        }

        // Truncate role_permissions for Super Admin
        DB::table('role_permissions')->where('role_id', $superAdminRole->id)->delete();

        // Get all permissions
        $permissions = DB::table('permissions')->get();

        if ($permissions->isEmpty()) {
            $this->command->error('No permissions found! Please run NewPermissionSeeder first.');
            return;
        }

        // Assign all permissions to Super Admin
        $rolePermissions = [];
        foreach ($permissions as $permission) {
            $rolePermissions[] = [
                'role_id' => $superAdminRole->id,
                'permission_id' => $permission->id,
            ];
        }

        // Insert in batches for performance
        foreach (array_chunk($rolePermissions, 100) as $batch) {
            DB::table('role_permissions')->insert($batch);
        }

        $this->command->info('✓ Role permissions seeded successfully!');
        $this->command->info("✓ Assigned {$permissions->count()} permissions to Super Admin role");
        $this->command->info('');
        $this->command->info('Permission Summary by Group:');

        // Show permission count by group
        $permissionsByGroup = $permissions->groupBy('group_name');
        foreach ($permissionsByGroup as $group => $perms) {
            $this->command->info("  - {$group}: {$perms->count()} permissions");
        }
    }
}
