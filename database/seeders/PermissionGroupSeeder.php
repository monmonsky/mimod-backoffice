<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            [
                'name' => 'product_full',
                'display_name' => 'Product Full Access',
                'description' => 'Full access to product management',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'product_read_only',
                'display_name' => 'Product Read Only',
                'description' => 'Read-only access to products',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'order_full',
                'display_name' => 'Order Full Access',
                'description' => 'Full access to order management',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'order_read_only',
                'display_name' => 'Order Read Only',
                'description' => 'Read-only access to orders',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'user_management_full',
                'display_name' => 'User Management Full',
                'description' => 'Full access to user management',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('permission_groups')->insert($groups);
    }
}
