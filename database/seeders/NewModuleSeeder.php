<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Based on frontend sidebar structure
     */
    public function run(): void
    {
        // Truncate table first
        DB::table('modules')->truncate();

        $sortOrder = 1;

        // ============================================
        // 1. OVERVIEW SECTION
        // ============================================
        DB::table('modules')->insert([
            'name' => 'dashboard',
            'display_name' => 'Dashboard',
            'description' => 'Dashboard overview',
            'icon' => 'lucide--monitor-dot',
            'parent_id' => null,
            'group_name' => 'overview',
            'route' => '/dashboards',
            'permission_name' => 'dashboard.view',
            'component' => 'Dashboard',
            'sort_order' => $sortOrder++,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ============================================
        // 2. ORDERS SECTION
        // ============================================

        // Orders - Main Menu
        $ordersId = DB::table('modules')->insertGetId([
            'name' => 'orders',
            'display_name' => 'All Orders',
            'description' => 'Order management',
            'icon' => 'lucide--shopping-cart',
            'parent_id' => null,
            'group_name' => 'orders',
            'route' => '/orders',
            'permission_name' => 'orders.view',
            'component' => 'AllOrders',
            'sort_order' => $sortOrder++,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Action Required - Parent Menu
        $ordersActionRequiredId = DB::table('modules')->insertGetId([
            'name' => 'orders-action-required',
            'display_name' => 'Action Required',
            'description' => 'Orders requiring action',
            'icon' => 'lucide--clipboard-list',
            'parent_id' => null,
            'group_name' => 'orders',
            'route' => null,
            'permission_name' => null,
            'component' => null,
            'sort_order' => $sortOrder++,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Unpaid Orders
        DB::table('modules')->insert([
            'name' => 'orders-unpaid',
            'display_name' => 'Unpaid Orders',
            'description' => 'Orders with unpaid status',
            'icon' => null,
            'parent_id' => $ordersActionRequiredId,
            'group_name' => 'orders',
            'route' => '/orders/unpaid',
            'permission_name' => 'orders.view',
            'component' => 'UnpaidOrders',
            'sort_order' => 1,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Ready to Ship
        DB::table('modules')->insert([
            'name' => 'orders-ready-to-ship',
            'display_name' => 'Ready to Ship',
            'description' => 'Orders ready to be shipped',
            'icon' => null,
            'parent_id' => $ordersActionRequiredId,
            'group_name' => 'orders',
            'route' => '/orders/ready-to-ship',
            'permission_name' => 'orders.view',
            'component' => 'ReadyToShip',
            'sort_order' => 2,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Order History - Parent Menu
        $ordersHistoryId = DB::table('modules')->insertGetId([
            'name' => 'orders-history',
            'display_name' => 'Order History',
            'description' => 'Historical orders',
            'icon' => 'lucide--history',
            'parent_id' => null,
            'group_name' => 'orders',
            'route' => null,
            'permission_name' => null,
            'component' => null,
            'sort_order' => $sortOrder++,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Shipped Orders
        DB::table('modules')->insert([
            'name' => 'orders-shipped',
            'display_name' => 'Shipped Orders',
            'description' => 'Orders that have been shipped',
            'icon' => null,
            'parent_id' => $ordersHistoryId,
            'group_name' => 'orders',
            'route' => '/orders/shipped',
            'permission_name' => 'orders.view',
            'component' => 'ShippedOrders',
            'sort_order' => 1,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Completed Orders
        DB::table('modules')->insert([
            'name' => 'orders-completed',
            'display_name' => 'Completed Orders',
            'description' => 'Completed orders',
            'icon' => null,
            'parent_id' => $ordersHistoryId,
            'group_name' => 'orders',
            'route' => '/orders/completed',
            'permission_name' => 'orders.view',
            'component' => 'CompletedOrders',
            'sort_order' => 2,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Cancelled Orders
        DB::table('modules')->insert([
            'name' => 'orders-cancelled',
            'display_name' => 'Cancelled Orders',
            'description' => 'Cancelled orders',
            'icon' => null,
            'parent_id' => $ordersHistoryId,
            'group_name' => 'orders',
            'route' => '/orders/cancelled',
            'permission_name' => 'orders.view',
            'component' => 'CancelledOrders',
            'sort_order' => 3,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ============================================
        // 3. CATALOGS SECTION
        // ============================================

        // Catalogs - Parent Menu
        $catalogsId = DB::table('modules')->insertGetId([
            'name' => 'catalogs',
            'display_name' => 'Catalogs',
            'description' => 'Product catalog management',
            'icon' => 'lucide--package',
            'parent_id' => null,
            'group_name' => 'catalogs',
            'route' => null,
            'permission_name' => null,
            'component' => null,
            'sort_order' => $sortOrder++,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Products
        DB::table('modules')->insert([
            'name' => 'products',
            'display_name' => 'Products',
            'description' => 'Product management',
            'icon' => null,
            'parent_id' => $catalogsId,
            'group_name' => 'catalogs',
            'route' => '/catalogs/products',
            'permission_name' => 'products.view',
            'component' => 'Products',
            'sort_order' => 1,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Brands
        DB::table('modules')->insert([
            'name' => 'brands',
            'display_name' => 'Brands',
            'description' => 'Brand management',
            'icon' => null,
            'parent_id' => $catalogsId,
            'group_name' => 'catalogs',
            'route' => '/catalogs/brands',
            'permission_name' => 'brands.view',
            'component' => 'Brands',
            'sort_order' => 2,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Categories
        DB::table('modules')->insert([
            'name' => 'categories',
            'display_name' => 'Categories',
            'description' => 'Category management',
            'icon' => null,
            'parent_id' => $catalogsId,
            'group_name' => 'catalogs',
            'route' => '/catalogs/categories',
            'permission_name' => 'categories.view',
            'component' => 'Categories',
            'sort_order' => 3,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Attributes
        DB::table('modules')->insert([
            'name' => 'attributes',
            'display_name' => 'Attributes',
            'description' => 'Product attribute management',
            'icon' => null,
            'parent_id' => $catalogsId,
            'group_name' => 'catalogs',
            'route' => '/catalogs/attributes',
            'permission_name' => 'attributes.view',
            'component' => 'Attributes',
            'sort_order' => 4,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ============================================
        // 4. CUSTOMERS SECTION
        // ============================================

        DB::table('modules')->insert([
            'name' => 'customers',
            'display_name' => 'Customers',
            'description' => 'Customer management',
            'icon' => 'lucide--users',
            'parent_id' => null,
            'group_name' => 'customers',
            'route' => '/customers',
            'permission_name' => 'customers.view',
            'component' => 'Customers',
            'sort_order' => $sortOrder++,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ============================================
        // 5. APPEARANCE SECTION
        // ============================================

        // Appearance - Parent Menu
        $appearanceId = DB::table('modules')->insertGetId([
            'name' => 'appearance',
            'display_name' => 'Appearance',
            'description' => 'Storefront appearance settings',
            'icon' => 'lucide--palette',
            'parent_id' => null,
            'group_name' => 'appearance',
            'route' => null,
            'permission_name' => null,
            'component' => null,
            'sort_order' => $sortOrder++,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Navigation
        DB::table('modules')->insert([
            'name' => 'navigation',
            'display_name' => 'Navigation',
            'description' => 'Navigation menu management',
            'icon' => null,
            'parent_id' => $appearanceId,
            'group_name' => 'appearance',
            'route' => '/appearance/navigation',
            'permission_name' => 'navigation.view',
            'component' => 'Navigation',
            'sort_order' => 1,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ============================================
        // 6. ACCESS CONTROL SECTION
        // ============================================

        // Access Control - Parent Menu
        $accessControlId = DB::table('modules')->insertGetId([
            'name' => 'access-control',
            'display_name' => 'Access Control',
            'description' => 'User access and permissions',
            'icon' => 'lucide--shield-check',
            'parent_id' => null,
            'group_name' => 'access_control',
            'route' => null,
            'permission_name' => null,
            'component' => null,
            'sort_order' => $sortOrder++,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Users
        DB::table('modules')->insert([
            'name' => 'users',
            'display_name' => 'Users',
            'description' => 'User management',
            'icon' => null,
            'parent_id' => $accessControlId,
            'group_name' => 'access_control',
            'route' => '/access-control/users',
            'permission_name' => 'users.view',
            'component' => 'Users',
            'sort_order' => 1,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Roles
        DB::table('modules')->insert([
            'name' => 'roles',
            'display_name' => 'Roles',
            'description' => 'Role management',
            'icon' => null,
            'parent_id' => $accessControlId,
            'group_name' => 'access_control',
            'route' => '/access-control/roles',
            'permission_name' => 'roles.view',
            'component' => 'Roles',
            'sort_order' => 2,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Permissions
        DB::table('modules')->insert([
            'name' => 'permissions',
            'display_name' => 'Permissions',
            'description' => 'Permission management',
            'icon' => null,
            'parent_id' => $accessControlId,
            'group_name' => 'access_control',
            'route' => '/access-control/permissions',
            'permission_name' => 'permissions.view',
            'component' => 'Permissions',
            'sort_order' => 3,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Modules
        DB::table('modules')->insert([
            'name' => 'modules',
            'display_name' => 'Modules',
            'description' => 'Module management',
            'icon' => null,
            'parent_id' => $accessControlId,
            'group_name' => 'access_control',
            'route' => '/access-control/modules',
            'permission_name' => 'modules.view',
            'component' => 'Modules',
            'sort_order' => 4,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // User Activity
        DB::table('modules')->insert([
            'name' => 'user-activity',
            'display_name' => 'User Activity',
            'description' => 'User activity logs',
            'icon' => null,
            'parent_id' => $accessControlId,
            'group_name' => 'access_control',
            'route' => '/access-control/user-activity',
            'permission_name' => 'user-activity.view',
            'component' => 'UserActivity',
            'sort_order' => 5,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Store Tokens
        DB::table('modules')->insert([
            'name' => 'store-tokens',
            'display_name' => 'Store Tokens',
            'description' => 'API token management',
            'icon' => null,
            'parent_id' => $accessControlId,
            'group_name' => 'access_control',
            'route' => '/access-control/store-tokens',
            'permission_name' => 'store-tokens.view',
            'component' => 'StoreTokens',
            'sort_order' => 6,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ============================================
        // 7. SETTINGS SECTION
        // ============================================

        DB::table('modules')->insert([
            'name' => 'settings',
            'display_name' => 'Settings',
            'description' => 'General settings',
            'icon' => 'lucide--settings',
            'parent_id' => null,
            'group_name' => 'settings',
            'route' => '/settings',
            'permission_name' => 'settings.view',
            'component' => 'Settings',
            'sort_order' => $sortOrder++,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Payment - Parent Menu
        $paymentId = DB::table('modules')->insertGetId([
            'name' => 'payment',
            'display_name' => 'Payment',
            'description' => 'Payment settings',
            'icon' => 'lucide--credit-card',
            'parent_id' => null,
            'group_name' => 'settings',
            'route' => null,
            'permission_name' => null,
            'component' => null,
            'sort_order' => $sortOrder++,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Payment Methods
        DB::table('modules')->insert([
            'name' => 'payment-methods',
            'display_name' => 'Payment Methods',
            'description' => 'Payment method management',
            'icon' => null,
            'parent_id' => $paymentId,
            'group_name' => 'settings',
            'route' => '/settings/payment-methods',
            'permission_name' => 'payment-methods.view',
            'component' => 'PaymentMethods',
            'sort_order' => 1,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Payment Configs
        DB::table('modules')->insert([
            'name' => 'payment-configs',
            'display_name' => 'Payment Configs',
            'description' => 'Payment configuration management',
            'icon' => null,
            'parent_id' => $paymentId,
            'group_name' => 'settings',
            'route' => '/settings/payment-configs',
            'permission_name' => 'payment-configs.view',
            'component' => 'PaymentConfigs',
            'sort_order' => 2,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Shipping - Parent Menu
        $shippingId = DB::table('modules')->insertGetId([
            'name' => 'shipping',
            'display_name' => 'Shipping',
            'description' => 'Shipping settings',
            'icon' => 'lucide--truck',
            'parent_id' => null,
            'group_name' => 'settings',
            'route' => null,
            'permission_name' => null,
            'component' => null,
            'sort_order' => $sortOrder++,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Shipping Methods
        DB::table('modules')->insert([
            'name' => 'shipping-methods',
            'display_name' => 'Shipping Methods',
            'description' => 'Shipping method management',
            'icon' => null,
            'parent_id' => $shippingId,
            'group_name' => 'settings',
            'route' => '/settings/shipping-methods',
            'permission_name' => 'shipping-methods.view',
            'component' => 'ShippingMethods',
            'sort_order' => 1,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Shipping Configs
        DB::table('modules')->insert([
            'name' => 'shipping-configs',
            'display_name' => 'Shipping Configs',
            'description' => 'Shipping configuration management',
            'icon' => null,
            'parent_id' => $shippingId,
            'group_name' => 'settings',
            'route' => '/settings/shipping-configs',
            'permission_name' => 'shipping-configs.view',
            'component' => 'ShippingConfigs',
            'sort_order' => 2,
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('✓ Modules seeded successfully!');
        $this->command->info('Summary:');
        $this->command->info('- 1 Dashboard module');
        $this->command->info('- 7 Orders modules (1 main + 2 parents + 4 children)');
        $this->command->info('- 5 Catalog modules (1 parent + 4 children)');
        $this->command->info('- 1 Customers module');
        $this->command->info('- 2 Appearance modules (1 parent + 1 child)');
        $this->command->info('- 7 Access Control modules (1 parent + 6 children)');
        $this->command->info('- 1 Settings module');
        $this->command->info('- 5 Payment/Shipping modules (2 parents + 4 children)');
        $this->command->info('Total: 29 modules');
    }
}
