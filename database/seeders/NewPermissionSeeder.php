<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates all permissions based on module actions
     */
    public function run(): void
    {
        // Truncate permissions table
        DB::table('permissions')->truncate();

        $permissions = [];

        // Helper function to create CRUD permissions
        $createCrudPermissions = function($module, $displayName) use (&$permissions) {
            $actions = ['view', 'show', 'create', 'update', 'delete'];
            $actionLabels = [
                'view' => 'View',
                'show' => 'Show Detail',
                'create' => 'Create',
                'update' => 'Update',
                'delete' => 'Delete',
            ];

            foreach ($actions as $action) {
                $permissions[] = [
                    'name' => "{$module}.{$action}",
                    'display_name' => "{$actionLabels[$action]} {$displayName}",
                    'description' => "{$actionLabels[$action]} {$displayName}",
                    'module' => $module,
                    'action' => $action,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        };

        // Helper function to add custom permission
        $addPermission = function($name, $displayName, $description, $module, $action) use (&$permissions) {
            $permissions[] = [
                'name' => $name,
                'display_name' => $displayName,
                'description' => $description,
                'module' => $module,
                'action' => $action,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        };

        // ============================================
        // 1. DASHBOARD PERMISSIONS
        // ============================================
        $addPermission('dashboard.view', 'View Dashboard', 'View dashboard overview and statistics', 'dashboard', 'view');

        // ============================================
        // 2. ORDERS PERMISSIONS
        // ============================================
        $createCrudPermissions('orders', 'Orders');
        $addPermission('orders.update-status', 'Update Order Status', 'Update order status', 'orders', 'update-status');
        $addPermission('orders.print', 'Print Order', 'Print order invoice/receipt', 'orders', 'print');

        // ============================================
        // 3. PRODUCTS PERMISSIONS
        // ============================================
        $createCrudPermissions('products', 'Products');
        $addPermission('products.update-stock', 'Update Product Stock', 'Update product stock quantity', 'products', 'update-stock');
        $addPermission('products.update-price', 'Update Product Price', 'Update product price', 'products', 'update-price');

        // ============================================
        // 4. BRANDS PERMISSIONS
        // ============================================
        $createCrudPermissions('brands', 'Brands');

        // ============================================
        // 5. CATEGORIES PERMISSIONS
        // ============================================
        $createCrudPermissions('categories', 'Categories');

        // ============================================
        // 6. ATTRIBUTES PERMISSIONS
        // ============================================
        $createCrudPermissions('attributes', 'Attributes');

        // ============================================
        // 7. CUSTOMERS PERMISSIONS
        // ============================================
        $createCrudPermissions('customers', 'Customers');

        // ============================================
        // 8. NAVIGATION PERMISSIONS
        // ============================================
        $createCrudPermissions('navigation', 'Navigation');

        // ============================================
        // 9. USERS PERMISSIONS
        // ============================================
        $createCrudPermissions('users', 'Users');
        $addPermission('users.assign-role', 'Assign User Role', 'Assign role to user', 'users', 'assign-role');

        // ============================================
        // 10. ROLES PERMISSIONS
        // ============================================
        $createCrudPermissions('roles', 'Roles');
        $addPermission('roles.assign-permission', 'Assign Role Permissions', 'Assign permissions to role', 'roles', 'assign-permission');

        // ============================================
        // 11. PERMISSIONS PERMISSIONS
        // ============================================
        $createCrudPermissions('permissions', 'Permissions');

        // ============================================
        // 12. MODULES PERMISSIONS
        // ============================================
        $createCrudPermissions('modules', 'Modules');

        // ============================================
        // 13. USER ACTIVITY PERMISSIONS
        // ============================================
        $addPermission('user-activity.view', 'View User Activity', 'View user activity logs', 'user-activity', 'view');
        $addPermission('user-activity.show', 'Show Activity Detail', 'Show user activity detail', 'user-activity', 'show');

        // ============================================
        // 14. STORE TOKENS PERMISSIONS
        // ============================================
        $createCrudPermissions('store-tokens', 'Store Tokens');
        $addPermission('store-tokens.revoke', 'Revoke Token', 'Revoke API token', 'store-tokens', 'revoke');

        // ============================================
        // 15. SETTINGS PERMISSIONS
        // ============================================
        $addPermission('settings.view', 'View Settings', 'View settings', 'settings', 'view');
        $addPermission('settings.update', 'Update Settings', 'Update settings', 'settings', 'update');

        // ============================================
        // 16. PAYMENT METHODS PERMISSIONS
        // ============================================
        $createCrudPermissions('payment-methods', 'Payment Methods');
        $addPermission('payment-methods.toggle-status', 'Toggle Payment Method Status', 'Activate/deactivate payment method', 'payment-methods', 'toggle-status');

        // ============================================
        // 17. PAYMENT CONFIGS PERMISSIONS
        // ============================================
        $createCrudPermissions('payment-configs', 'Payment Configs');

        // ============================================
        // 18. SHIPPING METHODS PERMISSIONS
        // ============================================
        $createCrudPermissions('shipping-methods', 'Shipping Methods');
        $addPermission('shipping-methods.toggle-status', 'Toggle Shipping Method Status', 'Activate/deactivate shipping method', 'shipping-methods', 'toggle-status');

        // ============================================
        // 19. SHIPPING CONFIGS PERMISSIONS
        // ============================================
        $createCrudPermissions('shipping-configs', 'Shipping Configs');

        // Insert all permissions
        DB::table('permissions')->insert($permissions);

        $this->command->info('✓ Permissions seeded successfully!');
        $this->command->info('Total Permissions: ' . count($permissions));
        $this->command->info('');
        $this->command->info('Permission Groups:');
        $this->command->info('1. dashboard - 1 permission');
        $this->command->info('2. orders - 7 permissions (5 CRUD + 2 special)');
        $this->command->info('3. products - 7 permissions (5 CRUD + 2 special)');
        $this->command->info('4. brands - 5 permissions (CRUD)');
        $this->command->info('5. categories - 5 permissions (CRUD)');
        $this->command->info('6. attributes - 5 permissions (CRUD)');
        $this->command->info('7. customers - 5 permissions (CRUD)');
        $this->command->info('8. navigation - 5 permissions (CRUD)');
        $this->command->info('9. users - 6 permissions (5 CRUD + 1 special)');
        $this->command->info('10. roles - 6 permissions (5 CRUD + 1 special)');
        $this->command->info('11. permissions - 5 permissions (CRUD)');
        $this->command->info('12. modules - 5 permissions (CRUD)');
        $this->command->info('13. user-activity - 2 permissions');
        $this->command->info('14. store-tokens - 6 permissions (5 CRUD + 1 special)');
        $this->command->info('15. settings - 2 permissions');
        $this->command->info('16. payment-methods - 6 permissions (5 CRUD + 1 special)');
        $this->command->info('17. payment-configs - 5 permissions (CRUD)');
        $this->command->info('18. shipping-methods - 6 permissions (5 CRUD + 1 special)');
        $this->command->info('19. shipping-configs - 5 permissions (CRUD)');
    }
}
