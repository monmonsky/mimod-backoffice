<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate table first
        DB::table('modules')->truncate();

        $modules = [
            // Overview
            [
                'name' => 'dashboard',
                'display_name' => 'Dashboard',
                'description' => 'Main dashboard overview',
                'icon' => 'lucide--monitor-dot',
                'route' => 'dashboard',
                'sort_order' => 1,
            ],

            // Catalog
            [
                'name' => 'products',
                'display_name' => 'Products',
                'description' => 'Product management',
                'icon' => 'lucide--package',
                'route' => null,
                'sort_order' => 2,
            ],

            // Sales
            [
                'name' => 'orders',
                'display_name' => 'Orders',
                'description' => 'Order management',
                'icon' => 'lucide--shopping-cart',
                'route' => null,
                'sort_order' => 3,
            ],
            [
                'name' => 'payments',
                'display_name' => 'Payments',
                'description' => 'Payment management',
                'icon' => 'lucide--credit-card',
                'route' => null,
                'sort_order' => 4,
            ],
            [
                'name' => 'shipments',
                'display_name' => 'Shipments',
                'description' => 'Shipment tracking',
                'icon' => 'lucide--truck',
                'route' => null,
                'sort_order' => 5,
            ],
            [
                'name' => 'carts',
                'display_name' => 'Carts',
                'description' => 'Shopping cart management',
                'icon' => 'lucide--shopping-bag',
                'route' => null,
                'sort_order' => 6,
            ],

            // Customers
            [
                'name' => 'customers',
                'display_name' => 'All Customers',
                'description' => 'Customer management',
                'icon' => 'lucide--users',
                'route' => null,
                'sort_order' => 7,
            ],
            [
                'name' => 'addresses',
                'display_name' => 'Addresses',
                'description' => 'Customer addresses',
                'icon' => 'lucide--map-pin',
                'route' => null,
                'sort_order' => 8,
            ],
            [
                'name' => 'notifications',
                'display_name' => 'Notifications',
                'description' => 'Customer notifications',
                'icon' => 'lucide--bell',
                'route' => null,
                'sort_order' => 9,
            ],

            // Marketing
            [
                'name' => 'promotions',
                'display_name' => 'Promotions',
                'description' => 'Marketing and promotions',
                'icon' => 'lucide--ticket',
                'route' => null,
                'sort_order' => 10,
            ],

            // Analytics
            [
                'name' => 'reports',
                'display_name' => 'Reports',
                'description' => 'Analytics and reports',
                'icon' => 'lucide--file-text',
                'route' => null,
                'sort_order' => 11,
            ],

            // Access Control
            [
                'name' => 'user-management',
                'display_name' => 'User Management',
                'description' => 'User and role management',
                'icon' => 'lucide--shield',
                'route' => null,
                'sort_order' => 12,
            ],
            [
                'name' => 'modules',
                'display_name' => 'Modules',
                'description' => 'Module management',
                'icon' => 'lucide--layout-grid',
                'route' => 'modules.index',
                'sort_order' => 13,
            ],

            // Settings
            [
                'name' => 'settings-generals',
                'display_name' => 'Generals',
                'description' => 'General settings',
                'icon' => 'lucide--settings',
                'route' => null,
                'sort_order' => 14,
            ],
            [
                'name' => 'settings-payments',
                'display_name' => 'Payments',
                'description' => 'Payment settings',
                'icon' => 'lucide--wallet',
                'route' => null,
                'sort_order' => 15,
            ],
            [
                'name' => 'settings-shippings',
                'display_name' => 'Shippings',
                'description' => 'Shipping settings',
                'icon' => 'lucide--truck',
                'route' => null,
                'sort_order' => 16,
            ],
        ];

        // Insert parent modules first and get their IDs
        $parentIds = [];
        foreach ($modules as $module) {
            $id = DB::table('modules')->insertGetId([
                'name' => $module['name'],
                'display_name' => $module['display_name'],
                'description' => $module['description'],
                'icon' => $module['icon'],
                'parent_id' => null,
                'route' => $module['route'],
                'component' => null,
                'sort_order' => $module['sort_order'],
                'is_active' => true,
                'is_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $parentIds[$module['name']] = $id;
        }

        // Insert child modules
        $childModules = [
            // Products children
            ['parent' => 'products', 'name' => 'all-products', 'display_name' => 'All Products', 'route' => null, 'sort_order' => 1],
            ['parent' => 'products', 'name' => 'add-product', 'display_name' => 'Add Product', 'route' => null, 'sort_order' => 2],
            ['parent' => 'products', 'name' => 'categories', 'display_name' => 'Categories', 'route' => null, 'sort_order' => 3],
            ['parent' => 'products', 'name' => 'brands', 'display_name' => 'Brands', 'route' => null, 'sort_order' => 4],
            ['parent' => 'products', 'name' => 'variants', 'display_name' => 'Variants', 'route' => null, 'sort_order' => 5],

            // Orders children
            ['parent' => 'orders', 'name' => 'all-orders', 'display_name' => 'All Orders', 'route' => null, 'sort_order' => 1],
            ['parent' => 'orders', 'name' => 'pending-orders', 'display_name' => 'Pending Orders', 'route' => null, 'sort_order' => 2],
            ['parent' => 'orders', 'name' => 'processing', 'display_name' => 'Processing', 'route' => null, 'sort_order' => 3],
            ['parent' => 'orders', 'name' => 'shipped', 'display_name' => 'Shipped', 'route' => null, 'sort_order' => 4],
            ['parent' => 'orders', 'name' => 'delivered', 'display_name' => 'Delivered', 'route' => null, 'sort_order' => 5],
            ['parent' => 'orders', 'name' => 'returns', 'display_name' => 'Returns', 'route' => null, 'sort_order' => 6],

            // Carts children
            ['parent' => 'carts', 'name' => 'active-carts', 'display_name' => 'Active Carts', 'route' => null, 'sort_order' => 1],
            ['parent' => 'carts', 'name' => 'abandoned-carts', 'display_name' => 'Abandoned Carts', 'route' => null, 'sort_order' => 2],

            // Promotions children
            ['parent' => 'promotions', 'name' => 'coupons', 'display_name' => 'Coupons', 'route' => 'promotions.coupons', 'sort_order' => 1],
            ['parent' => 'promotions', 'name' => 'coupon-usage', 'display_name' => 'Coupon Usage', 'route' => 'promotions.coupon-usage', 'sort_order' => 2],
            ['parent' => 'promotions', 'name' => 'campaigns', 'display_name' => 'Campaigns', 'route' => 'promotions.campaigns', 'sort_order' => 3],
            ['parent' => 'promotions', 'name' => 'email-campaigns', 'display_name' => 'Email Campaigns', 'route' => 'promotions.email-campaigns', 'sort_order' => 4],
            ['parent' => 'promotions', 'name' => 'email-templates', 'display_name' => 'Email Templates', 'route' => 'promotions.email-templates', 'sort_order' => 5],

            // Reports children
            ['parent' => 'reports', 'name' => 'sales-report', 'display_name' => 'Sales Report', 'route' => 'reports.sales', 'sort_order' => 1],
            ['parent' => 'reports', 'name' => 'product-performance', 'display_name' => 'Product Performance', 'route' => 'reports.product-performance', 'sort_order' => 2],
            ['parent' => 'reports', 'name' => 'customer-report', 'display_name' => 'Customer Report', 'route' => 'reports.customer', 'sort_order' => 3],
            ['parent' => 'reports', 'name' => 'payment-report', 'display_name' => 'Payment Report', 'route' => 'reports.payment', 'sort_order' => 4],
            ['parent' => 'reports', 'name' => 'inventory-report', 'display_name' => 'Inventory Report', 'route' => 'reports.inventory', 'sort_order' => 5],

            // User Management children
            ['parent' => 'user-management', 'name' => 'users', 'display_name' => 'Users', 'route' => 'user.index', 'sort_order' => 1],
            ['parent' => 'user-management', 'name' => 'roles', 'display_name' => 'Roles', 'route' => 'role.index', 'sort_order' => 2],
            ['parent' => 'user-management', 'name' => 'permissions', 'display_name' => 'Permissions', 'route' => 'permission.index', 'sort_order' => 3],
            ['parent' => 'user-management', 'name' => 'activity-logs', 'display_name' => 'Activity Logs', 'route' => 'activity-log.index', 'sort_order' => 4],
            ['parent' => 'user-management', 'name' => 'sessions', 'display_name' => 'Sessions', 'route' => 'session.index', 'sort_order' => 5],

            // Settings General children
            ['parent' => 'settings-generals', 'name' => 'store-info', 'display_name' => 'Store Info', 'route' => 'settings.generals.store', 'sort_order' => 1],
            ['parent' => 'settings-generals', 'name' => 'email-settings', 'display_name' => 'Email Settings', 'route' => 'settings.generals.email', 'sort_order' => 2],
            ['parent' => 'settings-generals', 'name' => 'seo-meta', 'display_name' => 'SEO & Meta', 'route' => 'settings.generals.seo', 'sort_order' => 3],
            ['parent' => 'settings-generals', 'name' => 'system-config', 'display_name' => 'System Config', 'route' => 'settings.generals.system', 'sort_order' => 4],
            ['parent' => 'settings-generals', 'name' => 'api-tokens', 'display_name' => 'API Tokens', 'route' => 'settings.generals.api-tokens', 'sort_order' => 5],

            // Settings Payment children
            ['parent' => 'settings-payments', 'name' => 'payment-methods', 'display_name' => 'Payment Methods', 'route' => 'settings.payments.methods', 'sort_order' => 1],
            ['parent' => 'settings-payments', 'name' => 'midtrans-config', 'display_name' => 'Midtrans Config', 'route' => 'settings.payments.midtrans-config', 'sort_order' => 2],
            ['parent' => 'settings-payments', 'name' => 'tax-settings', 'display_name' => 'Tax Settings', 'route' => 'settings.payments.tax-settings', 'sort_order' => 3],

            // Settings Shipping children
            ['parent' => 'settings-shippings', 'name' => 'shipping-methods', 'display_name' => 'Shipping Methods', 'route' => 'settings.shippings.methods', 'sort_order' => 1],
            ['parent' => 'settings-shippings', 'name' => 'rajaongkir-config', 'display_name' => 'RajaOngkir Config', 'route' => 'settings.shippings.rajaongkir-config', 'sort_order' => 2],
            ['parent' => 'settings-shippings', 'name' => 'origin-address', 'display_name' => 'Origin Address', 'route' => 'settings.shippings.origin-address', 'sort_order' => 3],
        ];

        foreach ($childModules as $child) {
            DB::table('modules')->insert([
                'name' => $child['name'],
                'display_name' => $child['display_name'],
                'description' => null,
                'icon' => null,
                'parent_id' => $parentIds[$child['parent']],
                'route' => $child['route'],
                'component' => null,
                'sort_order' => $child['sort_order'],
                'is_active' => true,
                'is_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
