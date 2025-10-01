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
            // Dashboard
            [
                'name' => 'dashboard',
                'display_name' => 'Dashboard',
                'description' => 'Dashboard overview',
                'icon' => 'lucide--monitor-dot',
                'route' => 'dashboard',
                'component' => 'Dashboard',
                'sort_order' => 1,
            ],
            // Access Control
            [
                'name' => 'access-control',
                'display_name' => 'Access Control',
                'description' => 'User access management',
                'icon' => 'lucide--shield',
                'route' => null,
                'component' => 'AccessControl',
                'sort_order' => 2,
            ],
            // Settings
            [
                'name' => 'settings',
                'display_name' => 'Settings',
                'description' => 'Application settings',
                'icon' => 'lucide--settings',
                'route' => null,
                'component' => 'Settings',
                'sort_order' => 3,
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
                'component' => $module['component'],
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
            // Access Control children
            ['parent' => 'access-control', 'name' => 'users', 'display_name' => 'Users', 'route' => 'user.index', 'sort_order' => 1],
            ['parent' => 'access-control', 'name' => 'roles', 'display_name' => 'Roles', 'route' => 'role.index', 'sort_order' => 2],
            ['parent' => 'access-control', 'name' => 'permissions', 'display_name' => 'Permissions', 'route' => 'permission.index', 'sort_order' => 3],
            ['parent' => 'access-control', 'name' => 'modules', 'display_name' => 'Modules', 'route' => 'modules.index', 'sort_order' => 4],
            ['parent' => 'access-control', 'name' => 'activity-logs', 'display_name' => 'Activity Logs', 'route' => 'activity-log.index', 'sort_order' => 5],

            // Settings children
            ['parent' => 'settings', 'name' => 'settings-generals', 'display_name' => 'Generals', 'route' => null, 'sort_order' => 1],
            ['parent' => 'settings', 'name' => 'settings-payments', 'display_name' => 'Payments', 'route' => null, 'sort_order' => 2],
            ['parent' => 'settings', 'name' => 'settings-shippings', 'display_name' => 'Shippings', 'route' => null, 'sort_order' => 3],
        ];

        foreach ($childModules as $child) {
            $childId = DB::table('modules')->insertGetId([
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

            // Store child IDs for grandchildren
            $parentIds[$child['name']] = $childId;
        }

        // Insert grandchild modules (Settings sub-menus)
        $grandChildModules = [
            // Settings > Generals sub-items
            ['parent' => 'settings-generals', 'name' => 'store-info', 'display_name' => 'Store Info', 'route' => 'settings.generals.store', 'sort_order' => 1],
            ['parent' => 'settings-generals', 'name' => 'email-settings', 'display_name' => 'Email Settings', 'route' => 'settings.generals.email', 'sort_order' => 2],
            ['parent' => 'settings-generals', 'name' => 'seo-meta', 'display_name' => 'SEO & Meta', 'route' => 'settings.generals.seo', 'sort_order' => 3],
            ['parent' => 'settings-generals', 'name' => 'system-config', 'display_name' => 'System Config', 'route' => 'settings.generals.system', 'sort_order' => 4],
            ['parent' => 'settings-generals', 'name' => 'api-tokens', 'display_name' => 'API Tokens', 'route' => 'settings.generals.api-tokens', 'sort_order' => 5],

            // Settings > Payments sub-items
            ['parent' => 'settings-payments', 'name' => 'payment-methods', 'display_name' => 'Payment Methods', 'route' => 'settings.payments.methods', 'sort_order' => 1],
            ['parent' => 'settings-payments', 'name' => 'midtrans-config', 'display_name' => 'Midtrans Config', 'route' => 'settings.payments.midtrans-config', 'sort_order' => 2],
            ['parent' => 'settings-payments', 'name' => 'tax-settings', 'display_name' => 'Tax Settings', 'route' => 'settings.payments.tax-settings', 'sort_order' => 3],

            // Settings > Shippings sub-items
            ['parent' => 'settings-shippings', 'name' => 'shipping-methods', 'display_name' => 'Shipping Methods', 'route' => 'settings.shippings.methods', 'sort_order' => 1],
            ['parent' => 'settings-shippings', 'name' => 'rajaongkir-config', 'display_name' => 'RajaOngkir Config', 'route' => 'settings.shippings.rajaongkir-config', 'sort_order' => 2],
            ['parent' => 'settings-shippings', 'name' => 'origin-address', 'display_name' => 'Origin Address', 'route' => 'settings.shippings.origin-address', 'sort_order' => 3],
        ];

        foreach ($grandChildModules as $grandChild) {
            DB::table('modules')->insert([
                'name' => $grandChild['name'],
                'display_name' => $grandChild['display_name'],
                'description' => null,
                'icon' => null,
                'parent_id' => $parentIds[$grandChild['parent']],
                'route' => $grandChild['route'],
                'component' => null,
                'sort_order' => $grandChild['sort_order'],
                'is_active' => true,
                'is_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Modules seeded successfully.');
    }
}
