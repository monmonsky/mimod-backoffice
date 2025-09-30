<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            'user' => 'User Management',
            'role' => 'Role Management',
            'permission' => 'Permission Management',
            'product' => 'Product Management',
            'category' => 'Category Management',
            'order' => 'Order Management',
            'customer' => 'Customer Management',
            'report' => 'Report Management',
            'setting' => 'System Settings',
        ];

        $actions = ['create', 'read', 'update', 'delete', 'export', 'import'];

        $permissions = [];

        foreach ($modules as $module => $displayModule) {
            foreach ($actions as $action) {
                $permissions[] = [
                    'name' => "{$module}.{$action}",
                    'display_name' => ucfirst($action) . ' ' . $displayModule,
                    'description' => "Permission to {$action} {$displayModule}",
                    'module' => $module,
                    'action' => $action,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Additional special permissions
        $specialPermissions = [
            [
                'name' => 'order.approve',
                'display_name' => 'Approve Orders',
                'description' => 'Permission to approve orders',
                'module' => 'order',
                'action' => 'approve',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'order.reject',
                'display_name' => 'Reject Orders',
                'description' => 'Permission to reject orders',
                'module' => 'order',
                'action' => 'reject',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'report.view_financial',
                'display_name' => 'View Financial Reports',
                'description' => 'Permission to view financial reports',
                'module' => 'report',
                'action' => 'view_financial',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        $permissions = array_merge($permissions, $specialPermissions);

        DB::table('permissions')->insert($permissions);
    }
}
