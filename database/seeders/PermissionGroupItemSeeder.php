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
        // Get permission groups
        $productFull = DB::table('permission_groups')->where('name', 'product_full')->first();
        $productReadOnly = DB::table('permission_groups')->where('name', 'product_read_only')->first();
        $orderFull = DB::table('permission_groups')->where('name', 'order_full')->first();
        $orderReadOnly = DB::table('permission_groups')->where('name', 'order_read_only')->first();
        $userManagementFull = DB::table('permission_groups')->where('name', 'user_management_full')->first();

        $groupItems = [];

        // Product Full - All product and category permissions
        $productPermissions = DB::table('permissions')
            ->whereIn('module', ['product', 'category'])
            ->get();

        foreach ($productPermissions as $permission) {
            $groupItems[] = [
                'group_id' => $productFull->id,
                'permission_id' => $permission->id,
            ];
        }

        // Product Read Only - Only read permissions
        $productReadPermissions = DB::table('permissions')
            ->whereIn('module', ['product', 'category'])
            ->where('action', 'read')
            ->get();

        foreach ($productReadPermissions as $permission) {
            $groupItems[] = [
                'group_id' => $productReadOnly->id,
                'permission_id' => $permission->id,
            ];
        }

        // Order Full - All order permissions
        $orderPermissions = DB::table('permissions')
            ->where('module', 'order')
            ->get();

        foreach ($orderPermissions as $permission) {
            $groupItems[] = [
                'group_id' => $orderFull->id,
                'permission_id' => $permission->id,
            ];
        }

        // Order Read Only - Only read permissions
        $orderReadPermissions = DB::table('permissions')
            ->where('module', 'order')
            ->where('action', 'read')
            ->get();

        foreach ($orderReadPermissions as $permission) {
            $groupItems[] = [
                'group_id' => $orderReadOnly->id,
                'permission_id' => $permission->id,
            ];
        }

        // User Management Full - All user, role, and permission permissions
        $userManagementPermissions = DB::table('permissions')
            ->whereIn('module', ['user', 'role', 'permission'])
            ->get();

        foreach ($userManagementPermissions as $permission) {
            $groupItems[] = [
                'group_id' => $userManagementFull->id,
                'permission_id' => $permission->id,
            ];
        }

        DB::table('permission_group_items')->insert($groupItems);
    }
}
