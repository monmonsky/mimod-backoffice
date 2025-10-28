<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users
        $superAdmin = DB::table('users')->where('email', 'superadmin@mimod.com')->first();
        $admin = DB::table('users')->where('email', 'admin@mimod.com')->first();
        $staff = DB::table('users')->where('email', 'staff@mimod.com')->first();
        $customer = DB::table('users')->where('email', 'customer@mimod.com')->first();

        // Get roles
        $superAdminRole = DB::table('roles')->where('name', 'super_admin')->first();
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        $staffRole = DB::table('roles')->where('name', 'staff')->first();
        $customerRole = DB::table('roles')->where('name', 'customer')->first();

        $userRoles = [
            [
                'user_id' => $superAdmin->id,
                'role_id' => $superAdminRole->id,
                'assigned_by' => null,
                'assigned_at' => now(),
                'expires_at' => null,
                'is_active' => true,
            ],
            [
                'user_id' => $admin->id,
                'role_id' => $adminRole->id,
                'assigned_by' => $superAdmin->id,
                'assigned_at' => now(),
                'expires_at' => null,
                'is_active' => true,
            ],
            [
                'user_id' => $staff->id,
                'role_id' => $staffRole->id,
                'assigned_by' => $superAdmin->id,
                'assigned_at' => now(),
                'expires_at' => null,
                'is_active' => true,
            ],
            [
                'user_id' => $customer->id,
                'role_id' => $customerRole->id,
                'assigned_by' => $superAdmin->id,
                'assigned_at' => now(),
                'expires_at' => null,
                'is_active' => true,
            ],
        ];

        DB::table('user_roles')->insert($userRoles);
    }
}
