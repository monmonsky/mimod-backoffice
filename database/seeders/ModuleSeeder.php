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
                'component' => 'Dashboard',
                'sort_order' => 1,
            ],
            [
                'name' => 'users',
                'display_name' => 'Users',
                'description' => 'User management',
                'icon' => 'lucide--users',
                'route' => 'user.index',
                'component' => 'Users',
                'sort_order' => 2,
            ],
            [
                'name' => 'roles',
                'display_name' => 'Roles',
                'description' => 'Role management',
                'icon' => 'lucide--shield',
                'route' => 'role.index',
                'component' => 'Roles',
                'sort_order' => 3,
            ],
            [
                'name' => 'permissions',
                'display_name' => 'Permissions',
                'description' => 'Permission management',
                'icon' => 'lucide--key-round',
                'route' => 'permission.index',
                'component' => 'Permissions',
                'sort_order' => 4,
            ],
            [
                'name' => 'modules',
                'display_name' => 'Modules',
                'description' => 'Module management',
                'icon' => 'lucide--layers',
                'route' => 'modules.index',
                'component' => 'Modules',
                'sort_order' => 5,
            ],
            [
                'name' => 'activity-logs',
                'display_name' => 'Activity Logs',
                'description' => 'System activity logs',
                'icon' => 'lucide--file-text',
                'route' => 'activity-log.index',
                'component' => 'ActivityLogs',
                'sort_order' => 6,
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
                'route' => $module['route'],
                'component' => $module['component'],
                'sort_order' => $module['sort_order'],
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
                'sort_order' => 7,
            ],
            [
                'name' => 'payments',
                'display_name' => 'Payments',
                'description' => 'Payment settings',
                'icon' => 'lucide--wallet',
                'route' => null,
                'sort_order' => 8,
            ],
            [
                'name' => 'shippings',
                'display_name' => 'Shippings',
                'description' => 'Shipping settings',
                'icon' => 'lucide--truck',
                'route' => null,
                'sort_order' => 9,
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
            ['parent' => 'generals', 'name' => 'store', 'display_name' => 'Store Info', 'route' => 'settings.generals.store', 'component' => 'StoreInfo', 'sort_order' => 1],
            ['parent' => 'generals', 'name' => 'email', 'display_name' => 'Email Settings', 'route' => 'settings.generals.email', 'component' => 'EmailSettings', 'sort_order' => 2],
            ['parent' => 'generals', 'name' => 'seo', 'display_name' => 'SEO & Meta', 'route' => 'settings.generals.seo', 'component' => 'SeoMeta', 'sort_order' => 3],
            ['parent' => 'generals', 'name' => 'system', 'display_name' => 'System Config', 'route' => 'settings.generals.system', 'component' => 'SystemConfig', 'sort_order' => 4],
            ['parent' => 'generals', 'name' => 'api-tokens', 'display_name' => 'API Tokens', 'route' => 'settings.generals.api-tokens', 'component' => 'ApiTokens', 'sort_order' => 5],

            // Payments children
            ['parent' => 'payments', 'name' => 'payment-methods', 'display_name' => 'Payment Methods', 'route' => 'settings.payments.methods', 'component' => 'PaymentMethods', 'sort_order' => 1],
            ['parent' => 'payments', 'name' => 'midtrans-config', 'display_name' => 'Midtrans Config', 'route' => 'settings.payments.midtrans-config', 'component' => 'MidtransConfig', 'sort_order' => 2],
            ['parent' => 'payments', 'name' => 'tax-settings', 'display_name' => 'Tax Settings', 'route' => 'settings.payments.tax-settings', 'component' => 'TaxSettings', 'sort_order' => 3],

            // Shippings children
            ['parent' => 'shippings', 'name' => 'shipping-methods', 'display_name' => 'Shipping Methods', 'route' => 'settings.shippings.methods', 'component' => 'ShippingMethods', 'sort_order' => 1],
            ['parent' => 'shippings', 'name' => 'rajaongkir-config', 'display_name' => 'RajaOngkir Config', 'route' => 'settings.shippings.rajaongkir-config', 'component' => 'RajaOngkirConfig', 'sort_order' => 2],
            ['parent' => 'shippings', 'name' => 'origin-address', 'display_name' => 'Origin Address', 'route' => 'settings.shippings.origin-address', 'component' => 'OriginAddress', 'sort_order' => 3],
        ];

        foreach ($settingsChildren as $child) {
            DB::table('modules')->insert([
                'name' => $child['name'],
                'display_name' => $child['display_name'],
                'description' => null,
                'icon' => null,
                'parent_id' => $parentIds[$child['parent']],
                'route' => $child['route'],
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
        $this->command->info('- 3 settings parent modules (generals, payments, shippings)');
        $this->command->info('- 13 settings children (5 generals + 3 payments + 3 shippings + 2 more)');
        $this->command->info('Total: 22 modules');
    }
}
