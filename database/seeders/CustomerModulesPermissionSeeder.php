<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerModulesPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create the Customers permission group
        $customersGroup = DB::table('permission_groups')
            ->where('name', 'Customers')
            ->first();

        if (!$customersGroup) {
            echo "Creating Customers permission group...\n";
            $groupId = DB::table('permission_groups')->insertGetId([
                'name' => 'Customers',
                'display_name' => 'Customers',
                'description' => 'Customer management permissions',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $customersGroup = DB::table('permission_groups')->where('id', $groupId)->first();
            echo "✓ Created Customers permission group\n\n";
        }

        $permissions = [
            // Customer Groups
            ['name' => 'customer-groups', 'display_name' => 'View Customer Groups', 'action' => 'view', 'description' => 'View customer groups'],
            ['name' => 'customer-groups', 'display_name' => 'Create Customer Groups', 'action' => 'create', 'description' => 'Create new customer groups'],
            ['name' => 'customer-groups', 'display_name' => 'Update Customer Groups', 'action' => 'update', 'description' => 'Update customer groups'],
            ['name' => 'customer-groups', 'display_name' => 'Delete Customer Groups', 'action' => 'delete', 'description' => 'Delete customer groups'],

            // Customer Segments
            ['name' => 'customer-segments', 'display_name' => 'View Customer Segments', 'action' => 'view', 'description' => 'View customer segments'],
            ['name' => 'customer-segments', 'display_name' => 'Create Customer Segments', 'action' => 'create', 'description' => 'Create new customer segments'],
            ['name' => 'customer-segments', 'display_name' => 'Update Customer Segments', 'action' => 'update', 'description' => 'Update customer segments'],
            ['name' => 'customer-segments', 'display_name' => 'Delete Customer Segments', 'action' => 'delete', 'description' => 'Delete customer segments'],

            // Customer Loyalty
            ['name' => 'loyalty', 'display_name' => 'View Loyalty Programs', 'action' => 'view', 'description' => 'View loyalty programs and transactions'],
            ['name' => 'loyalty', 'display_name' => 'Create Loyalty Programs', 'action' => 'create', 'description' => 'Create loyalty programs and transactions'],
            ['name' => 'loyalty', 'display_name' => 'Update Loyalty Programs', 'action' => 'update', 'description' => 'Update loyalty programs'],
            ['name' => 'loyalty', 'display_name' => 'Delete Loyalty Programs', 'action' => 'delete', 'description' => 'Delete loyalty programs'],

            // Customer Reviews
            ['name' => 'reviews', 'display_name' => 'View Customer Reviews', 'action' => 'view', 'description' => 'View customer reviews'],
            ['name' => 'reviews', 'display_name' => 'Approve Customer Reviews', 'action' => 'approve', 'description' => 'Approve customer reviews'],
            ['name' => 'reviews', 'display_name' => 'Update Customer Reviews', 'action' => 'update', 'description' => 'Update and respond to customer reviews'],
            ['name' => 'reviews', 'display_name' => 'Delete Customer Reviews', 'action' => 'delete', 'description' => 'Delete customer reviews'],
        ];

        foreach ($permissions as $permission) {
            // Check if permission already exists
            $existing = DB::table('permissions')
                ->where('module', 'customers')
                ->where('name', $permission['name'])
                ->where('action', $permission['action'])
                ->first();

            if (!$existing) {
                DB::table('permissions')->insert([
                    'name' => $permission['name'],
                    'display_name' => $permission['display_name'],
                    'description' => $permission['description'],
                    'module' => 'customers',
                    'action' => $permission['action'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                echo "✓ Created permission: {$permission['display_name']}\n";
            } else {
                echo "- Permission already exists: {$permission['display_name']}\n";
            }
        }

        // Assign all permissions to Super Admin role
        $superAdminRole = DB::table('roles')->where('name', 'Super Admin')->first();

        if ($superAdminRole) {
            $newPermissions = DB::table('permissions')
                ->where('module', 'customers')
                ->pluck('id');

            foreach ($newPermissions as $permissionId) {
                $exists = DB::table('role_permissions')
                    ->where('role_id', $superAdminRole->id)
                    ->where('permission_id', $permissionId)
                    ->exists();

                if (!$exists) {
                    DB::table('role_permissions')->insert([
                        'role_id' => $superAdminRole->id,
                        'permission_id' => $permissionId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            echo "✓ Assigned all permissions to Super Admin role\n";
        }

        echo "\nCustomer modules permissions seeded successfully!\n";
    }
}
