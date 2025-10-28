<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarketingModulesSeeder extends Seeder
{
    public function run(): void
    {
        echo "Seeding marketing modules...\n";

        // Get or create Marketing parent module
        $marketingModule = DB::table('modules')
            ->where('name', 'marketing')
            ->where('parent_id', null)
            ->first();

        if (!$marketingModule) {
            $marketingModuleId = DB::table('modules')->insertGetId([
                'name' => 'marketing',
                'display_name' => 'Marketing',
                'group_name' => 'Marketing',
                'icon' => 'lucide--megaphone',
                'route' => null,
                'parent_id' => null,
                'sort_order' => 5,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $marketingModuleId = $marketingModule->id;
        }

        // Marketing children modules
        $marketingChildren = [
            [
                'name' => 'coupons',
                'display_name' => 'Coupons',
                'route' => 'marketing.coupons.index',
                'icon' => 'lucide--ticket',
                'sort_order' => 1,
            ],
            [
                'name' => 'flash-sales',
                'display_name' => 'Flash Sales',
                'route' => 'marketing.flash-sales.index',
                'icon' => 'lucide--zap',
                'sort_order' => 2,
            ],
            [
                'name' => 'bundle-deals',
                'display_name' => 'Bundle Deals',
                'route' => 'marketing.bundle-deals.index',
                'icon' => 'lucide--package-2',
                'sort_order' => 3,
            ],
        ];

        foreach ($marketingChildren as $child) {
            $exists = DB::table('modules')
                ->where('name', $child['name'])
                ->where('parent_id', $marketingModuleId)
                ->exists();

            if (!$exists) {
                DB::table('modules')->insert([
                    'name' => $child['name'],
                    'display_name' => $child['display_name'],
                    'group_name' => 'Marketing',
                    'icon' => $child['icon'],
                    'route' => $child['route'],
                    'parent_id' => $marketingModuleId,
                    'sort_order' => $child['sort_order'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                echo "Created module: {$child['name']}\n";
            } else {
                echo "Module already exists: {$child['name']}\n";
            }
        }

        echo "Marketing modules seeded successfully!\n";
    }
}
