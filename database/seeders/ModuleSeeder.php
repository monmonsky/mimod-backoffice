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

        // Level 1: Direct modules (dashboard + access control)
        $directModules = [
            [
                'name' => 'dashboard',
                'display_name' => 'Dashboard',
                'description' => 'Dashboard overview',
                'icon' => 'lucide--monitor-dot',
                'route' => 'dashboard',
                'permission_name' => 'dashboard.view',
                'component' => 'Dashboard',
                'sort_order' => 1,
                'group_name' => 'overview',
            ],
            [
                'name' => 'users',
                'display_name' => 'Users',
                'description' => 'User management',
                'icon' => 'lucide--users',
                'route' => 'user.index',
                'permission_name' => 'access-control.users.view',
                'component' => 'Users',
                'sort_order' => 2,
                'group_name' => 'access_control',
            ],
            [
                'name' => 'roles',
                'display_name' => 'Roles',
                'description' => 'Role management',
                'icon' => 'lucide--shield',
                'route' => 'role.index',
                'permission_name' => 'access-control.roles.view',
                'component' => 'Roles',
                'sort_order' => 3,
                'group_name' => 'access_control',
            ],
            [
                'name' => 'permissions',
                'display_name' => 'Permissions',
                'description' => 'Permission management',
                'icon' => 'lucide--key-round',
                'route' => 'permission.index',
                'permission_name' => 'access-control.permissions.view',
                'component' => 'Permissions',
                'sort_order' => 4,
                'group_name' => 'access_control',
            ],
            [
                'name' => 'modules',
                'display_name' => 'Modules',
                'description' => 'Module management',
                'icon' => 'lucide--layers',
                'route' => 'modules.index',
                'permission_name' => 'access-control.modules.view',
                'component' => 'Modules',
                'sort_order' => 5,
                'group_name' => 'access_control',
            ],
            [
                'name' => 'user-activities',
                'display_name' => 'User Activities',
                'description' => 'User activity logs',
                'icon' => 'lucide--file-text',
                'route' => 'access-control.user-activities.index',
                'permission_name' => 'access-control.user-activities.view',
                'component' => 'UserActivities',
                'sort_order' => 6,
                'group_name' => 'access_control',
            ],
        ];

        // Insert direct modules
        foreach ($directModules as $module) {
            DB::table('modules')->insert([
                'name' => $module['name'],
                'display_name' => $module['display_name'],
                'description' => $module['description'],
                'icon' => $module['icon'],
                'parent_id' => null,
                'group_name' => $module['group_name'],
                'route' => $module['route'],
                'permission_name' => $module['permission_name'] ?? null,
                'component' => $module['component'],
                'sort_order' => $module['sort_order'],
                'is_active' => true,
                'is_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $catalogModules = [
            [
                'name' => 'products',
                'display_name' => 'Products',
                'description' => 'Products management',
                'icon' => 'lucide--box',
                'route' => null,
                'component' => 'Products',
                'sort_order' => 7,
                'group_name' => 'catalog',
            ],
        ];

        $catalogParentIds = [];
        foreach ($catalogModules as $catalog) {
            $id = DB::table('modules')->insertGetId([
                'name' => $catalog['name'],
                'display_name' => $catalog['display_name'],
                'description' => $catalog['description'],
                'icon' => $catalog['icon'],
                'parent_id' => null,
                'group_name' => $catalog['group_name'],
                'route' => $catalog['route'],
                'component' => null,
                'sort_order' => $catalog['sort_order'],
                'is_active' => true,
                'is_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $catalogParentIds[$catalog['name']] = $id;
        }

        $catalogsChildren = [
            // Products children
            ['parent' => 'products', 'name' => 'all-products', 'display_name' => 'All Products', 'route' => 'catalog.products.all-products', 'permission_name' => 'catalog.products.all-products.view', 'component' => 'AllProducts', 'sort_order' => 1],
            ['parent' => 'products', 'name' => 'add-products', 'display_name' => 'Add Products', 'route' => 'catalog.products.add-products', 'permission_name' => 'catalog.products.add-products.view', 'component' => 'AddProducts', 'sort_order' => 2],
            ['parent' => 'products', 'name' => 'categories', 'display_name' => 'Categories', 'route' => 'catalog.products.categories', 'permission_name' => 'catalog.products.categories.view', 'component' => 'Categories', 'sort_order' => 3],
            ['parent' => 'products', 'name' => 'brands', 'display_name' => 'Brands', 'route' => 'catalog.products.brands', 'permission_name' => 'catalog.products.brands.view', 'component' => 'Brands', 'sort_order' => 4],
            ['parent' => 'products', 'name' => 'variants', 'display_name' => 'Variants', 'route' => 'catalog.products.variants', 'permission_name' => 'catalog.products.variants.view', 'component' => 'Variants', 'sort_order' => 5],
        ];

        foreach ($catalogsChildren as $child) {
            DB::table('modules')->insert([
                'name' => $child['name'],
                'display_name' => $child['display_name'],
                'description' => null,
                'icon' => null,
                'parent_id' => $catalogParentIds[$child['parent']],
                'route' => $child['route'],
                'permission_name' => $child['permission_name'] ?? null,
                'component' => $child['component'],
                'sort_order' => $child['sort_order'],
                'is_active' => true,
                'is_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }


        // Level 1: Settings parent modules (generals, payments, shippings)
        $settingsParents = [
            [
                'name' => 'generals',
                'display_name' => 'Generals',
                'description' => 'General settings',
                'icon' => 'lucide--settings',
                'route' => null,
                'sort_order' => 8,
                'group_name' => 'settings',
            ],
            [
                'name' => 'payments',
                'display_name' => 'Payments',
                'description' => 'Payment settings',
                'icon' => 'lucide--wallet',
                'route' => null,
                'sort_order' => 9,
                'group_name' => 'settings',
            ],
            [
                'name' => 'shippings',
                'display_name' => 'Shippings',
                'description' => 'Shipping settings',
                'icon' => 'lucide--truck',
                'route' => null,
                'sort_order' => 10,
                'group_name' => 'settings',
            ],
        ];

        $parentIds = [];
        foreach ($settingsParents as $parent) {
            $id = DB::table('modules')->insertGetId([
                'name' => $parent['name'],
                'display_name' => $parent['display_name'],
                'description' => $parent['description'],
                'icon' => $parent['icon'],
                'parent_id' => null,
                'group_name' => $parent['group_name'],
                'route' => $parent['route'],
                'component' => null,
                'sort_order' => $parent['sort_order'],
                'is_active' => true,
                'is_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $parentIds[$parent['name']] = $id;
        }

        // Level 2: Settings children
        $settingsChildren = [
            // Generals children
            ['parent' => 'generals', 'name' => 'store', 'display_name' => 'Store Info', 'route' => 'settings.generals.store', 'permission_name' => 'settings.generals.store.view', 'component' => 'StoreInfo', 'sort_order' => 1],
            ['parent' => 'generals', 'name' => 'email', 'display_name' => 'Email Settings', 'route' => 'settings.generals.email', 'permission_name' => 'settings.generals.email.view', 'component' => 'EmailSettings', 'sort_order' => 2],
            ['parent' => 'generals', 'name' => 'seo', 'display_name' => 'SEO & Meta', 'route' => 'settings.generals.seo', 'permission_name' => 'settings.generals.seo.view', 'component' => 'SeoMeta', 'sort_order' => 3],
            ['parent' => 'generals', 'name' => 'system', 'display_name' => 'System Config', 'route' => 'settings.generals.system', 'permission_name' => 'settings.generals.system.view', 'component' => 'SystemConfig', 'sort_order' => 4],
            ['parent' => 'generals', 'name' => 'api-tokens', 'display_name' => 'API Tokens', 'route' => 'settings.generals.api-tokens', 'permission_name' => 'settings.generals.api-tokens.view', 'component' => 'ApiTokens', 'sort_order' => 5],

            // Payments children
            ['parent' => 'payments', 'name' => 'payment-methods', 'display_name' => 'Payment Methods', 'route' => 'settings.payments.methods', 'permission_name' => 'settings.payments.methods.view', 'component' => 'PaymentMethods', 'sort_order' => 1],
            ['parent' => 'payments', 'name' => 'midtrans-config', 'display_name' => 'Midtrans Config', 'route' => 'settings.payments.midtrans-config', 'permission_name' => 'settings.payments.midtrans.view', 'component' => 'MidtransConfig', 'sort_order' => 2],
            ['parent' => 'payments', 'name' => 'tax-settings', 'display_name' => 'Tax Settings', 'route' => 'settings.payments.tax-settings', 'permission_name' => 'settings.payments.tax.view', 'component' => 'TaxSettings', 'sort_order' => 3],

            // Shippings children
            ['parent' => 'shippings', 'name' => 'shipping-methods', 'display_name' => 'Shipping Methods', 'route' => 'settings.shippings.methods', 'permission_name' => 'settings.shippings.methods.view', 'component' => 'ShippingMethods', 'sort_order' => 1],
            ['parent' => 'shippings', 'name' => 'rajaongkir-config', 'display_name' => 'RajaOngkir Config', 'route' => 'settings.shippings.rajaongkir-config', 'permission_name' => 'settings.shippings.rajaongkir.view', 'component' => 'RajaOngkirConfig', 'sort_order' => 2],
            ['parent' => 'shippings', 'name' => 'origin-address', 'display_name' => 'Origin Address', 'route' => 'settings.shippings.origin-address', 'permission_name' => 'settings.shippings.origin.view', 'component' => 'OriginAddress', 'sort_order' => 3],
        ];

        foreach ($settingsChildren as $child) {
            DB::table('modules')->insert([
                'name' => $child['name'],
                'display_name' => $child['display_name'],
                'description' => null,
                'icon' => null,
                'parent_id' => $parentIds[$child['parent']],
                'route' => $child['route'],
                'permission_name' => $child['permission_name'] ?? null,
                'component' => $child['component'],
                'sort_order' => $child['sort_order'],
                'is_active' => true,
                'is_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Level 1: Reports parent module
        $reportsParent = [
            'name' => 'reports',
            'display_name' => 'Reports',
            'description' => 'Business reports and analytics',
            'icon' => 'lucide--line-chart',
            'route' => null,
            'sort_order' => 11,
            'group_name' => 'reports',
        ];

        $reportsParentId = DB::table('modules')->insertGetId([
            'name' => $reportsParent['name'],
            'display_name' => $reportsParent['display_name'],
            'description' => $reportsParent['description'],
            'icon' => $reportsParent['icon'],
            'parent_id' => null,
            'group_name' => $reportsParent['group_name'],
            'route' => $reportsParent['route'],
            'component' => null,
            'sort_order' => $reportsParent['sort_order'],
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Level 2: Reports children
        $reportsChildren = [
            ['name' => 'sales-report', 'display_name' => 'Sales Report', 'route' => 'reports.sales', 'permission_name' => 'reports.sales.view', 'component' => 'SalesReport', 'sort_order' => 1],
            ['name' => 'revenue-report', 'display_name' => 'Revenue Report', 'route' => 'reports.revenue', 'permission_name' => 'reports.revenue.view', 'component' => 'RevenueReport', 'sort_order' => 2],
            ['name' => 'product-performance', 'display_name' => 'Product Performance', 'route' => 'reports.product-performance', 'permission_name' => 'reports.product-performance.view', 'component' => 'ProductPerformance', 'sort_order' => 3],
            ['name' => 'inventory-report', 'display_name' => 'Inventory Report', 'route' => 'reports.inventory', 'permission_name' => 'reports.inventory.view', 'component' => 'InventoryReport', 'sort_order' => 4],
        ];

        foreach ($reportsChildren as $child) {
            DB::table('modules')->insert([
                'name' => $child['name'],
                'display_name' => $child['display_name'],
                'description' => null,
                'icon' => null,
                'parent_id' => $reportsParentId,
                'route' => $child['route'],
                'permission_name' => $child['permission_name'] ?? null,
                'component' => $child['component'],
                'sort_order' => $child['sort_order'],
                'is_active' => true,
                'is_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Level 1: Customers parent module
        $customersParent = [
            'name' => 'customers',
            'display_name' => 'Customers',
            'description' => 'Customer management and segmentation',
            'icon' => 'lucide--users',
            'route' => null,
            'sort_order' => 11,
            'group_name' => 'customers',
        ];

        $customersParentId = DB::table('modules')->insertGetId([
            'name' => $customersParent['name'],
            'display_name' => $customersParent['display_name'],
            'description' => $customersParent['description'],
            'icon' => $customersParent['icon'],
            'parent_id' => null,
            'group_name' => $customersParent['group_name'],
            'route' => $customersParent['route'],
            'component' => null,
            'sort_order' => $customersParent['sort_order'],
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Level 2: Customers children
        $customersChildren = [
            ['name' => 'all-customers', 'display_name' => 'All Customers', 'route' => 'customers.all-customers.index', 'permission_name' => 'customers.all-customers.view', 'component' => 'AllCustomers', 'sort_order' => 1],
            ['name' => 'customer-segments', 'display_name' => 'Customer Segments', 'route' => 'customers.customer-segments.index', 'permission_name' => 'customers.customer-segments.view', 'component' => 'CustomerSegments', 'sort_order' => 2],
            ['name' => 'vip-customers', 'display_name' => 'VIP Customers', 'route' => 'customers.vip-customers.index', 'permission_name' => 'customers.vip-customers.view', 'component' => 'VipCustomers', 'sort_order' => 3],
        ];

        foreach ($customersChildren as $child) {
            DB::table('modules')->insert([
                'name' => $child['name'],
                'display_name' => $child['display_name'],
                'description' => null,
                'icon' => null,
                'parent_id' => $customersParentId,
                'route' => $child['route'],
                'permission_name' => $child['permission_name'] ?? null,
                'component' => $child['component'],
                'sort_order' => $child['sort_order'],
                'is_active' => true,
                'is_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Level 1: Orders parent module
        $ordersParent = [
            'name' => 'orders',
            'display_name' => 'Orders',
            'description' => 'Order management',
            'icon' => 'lucide--shopping-cart',
            'route' => null,
            'sort_order' => 12,
            'group_name' => 'orders',
        ];

        $ordersParentId = DB::table('modules')->insertGetId([
            'name' => $ordersParent['name'],
            'display_name' => $ordersParent['display_name'],
            'description' => $ordersParent['description'],
            'icon' => $ordersParent['icon'],
            'parent_id' => null,
            'group_name' => $ordersParent['group_name'],
            'route' => $ordersParent['route'],
            'component' => null,
            'sort_order' => $ordersParent['sort_order'],
            'is_active' => true,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Level 2: Orders children
        $ordersChildren = [
            ['name' => 'all-orders', 'display_name' => 'All Orders', 'route' => 'orders.all-orders.index', 'permission_name' => 'orders.all-orders.view', 'component' => 'AllOrders', 'sort_order' => 1],
            ['name' => 'pending-orders', 'display_name' => 'Pending Orders', 'route' => 'orders.pending-orders.index', 'permission_name' => 'orders.pending-orders.view', 'component' => 'PendingOrders', 'sort_order' => 2],
            ['name' => 'processing-orders', 'display_name' => 'Processing Orders', 'route' => 'orders.processing-orders.index', 'permission_name' => 'orders.processing-orders.view', 'component' => 'ProcessingOrders', 'sort_order' => 3],
            ['name' => 'shipped-orders', 'display_name' => 'Shipped Orders', 'route' => 'orders.shipped-orders.index', 'permission_name' => 'orders.shipped-orders.view', 'component' => 'ShippedOrders', 'sort_order' => 4],
            ['name' => 'completed-orders', 'display_name' => 'Completed Orders', 'route' => 'orders.completed-orders.index', 'permission_name' => 'orders.completed-orders.view', 'component' => 'CompletedOrders', 'sort_order' => 5],
            ['name' => 'cancelled-orders', 'display_name' => 'Cancelled Orders', 'route' => 'orders.cancelled-orders.index', 'permission_name' => 'orders.cancelled-orders.view', 'component' => 'CancelledOrders', 'sort_order' => 6],
        ];

        foreach ($ordersChildren as $child) {
            DB::table('modules')->insert([
                'name' => $child['name'],
                'display_name' => $child['display_name'],
                'description' => null,
                'icon' => null,
                'parent_id' => $ordersParentId,
                'route' => $child['route'],
                'permission_name' => $child['permission_name'] ?? null,
                'component' => $child['component'],
                'sort_order' => $child['sort_order'],
                'is_active' => true,
                'is_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Modules seeded successfully.');
        $this->command->info('- 6 direct modules (dashboard + 5 access control)');
        $this->command->info('- 1 catalog parent module (products)');
        $this->command->info('- 5 catalog children');
        $this->command->info('- 3 settings parent modules (generals, payments, shippings)');
        $this->command->info('- 13 settings children (5 generals + 3 payments + 3 shippings + 2 more)');
        $this->command->info('- 1 reports parent module');
        $this->command->info('- 4 reports children (sales, revenue, product-performance, inventory)');
        $this->command->info('- 1 customers parent module');
        $this->command->info('- 3 customers children (all-customers, customer-segments, vip-customers)');
        $this->command->info('- 1 orders parent module');
        $this->command->info('- 6 orders children (all, pending, processing, shipped, completed, cancelled)');
        $this->command->info('Total: 40 modules');
    }
}
