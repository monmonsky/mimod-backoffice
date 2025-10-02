<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionGroupItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('permission_group_items')->truncate();

        $groupItems = [];

        // Dashboard Management Group
        $dashboardManagement = DB::table('permission_groups')
            ->where('name', 'dashboard_management')
            ->first();

        if ($dashboardManagement) {
            $dashboardPermissions = DB::table('permissions')
                ->where('name', 'LIKE', 'dashboard.%')
                ->get();

            foreach ($dashboardPermissions as $permission) {
                $groupItems[] = [
                    'group_id' => $dashboardManagement->id,
                    'permission_id' => $permission->id,
                ];
            }
        }

        // Access Control Management Group
        $accessControlManagement = DB::table('permission_groups')
            ->where('name', 'access_control_management')
            ->first();

        if ($accessControlManagement) {
            $accessControlPermissions = DB::table('permissions')
                ->where('name', 'LIKE', 'access-control.%')
                ->get();

            foreach ($accessControlPermissions as $permission) {
                $groupItems[] = [
                    'group_id' => $accessControlManagement->id,
                    'permission_id' => $permission->id,
                ];
            }
        }

        // Settings Management Group
        $settingsManagement = DB::table('permission_groups')
            ->where('name', 'settings_management')
            ->first();

        if ($settingsManagement) {
            $settingsPermissions = DB::table('permissions')
                ->where('name', 'LIKE', 'settings.%')
                ->get();

            foreach ($settingsPermissions as $permission) {
                $groupItems[] = [
                    'group_id' => $settingsManagement->id,
                    'permission_id' => $permission->id,
                ];
            }
        }

        // Catalog Management Group
        $settingsManagement = DB::table('permission_groups')
            ->where('name', 'catalog_management')
            ->first();

        if ($settingsManagement) {
            $settingsPermissions = DB::table('permissions')
                ->where('name', 'LIKE', 'catalog.%')
                ->get();

            foreach ($settingsPermissions as $permission) {
                $groupItems[] = [
                    'group_id' => $settingsManagement->id,
                    'permission_id' => $permission->id,
                ];
            }
        }

        if (!empty($groupItems)) {
            DB::table('permission_group_items')->insert($groupItems);
        }

        $this->command->info('Permission group items seeded successfully.');
    }
}
