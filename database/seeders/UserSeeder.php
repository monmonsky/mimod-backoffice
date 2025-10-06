<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get role IDs
        $superAdminRole = DB::table('roles')->where('name', 'super_admin')->first();
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        $staffRole = DB::table('roles')->where('name', 'staff')->first();
        $customerRole = DB::table('roles')->where('name', 'customer')->first();

        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@mimod.com',
                'phone' => '081234567890',
                'password' => Hash::make('password'),
                'role_id' => $superAdminRole->id,
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'status' => 'active',
                'two_factor_enabled' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@mimod.com',
                'phone' => '081234567891',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'status' => 'active',
                'two_factor_enabled' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Staff User',
                'email' => 'staff@mimod.com',
                'phone' => '081234567892',
                'password' => Hash::make('password'),
                'role_id' => $staffRole->id,
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'status' => 'active',
                'two_factor_enabled' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Customer User',
                'email' => 'customer@mimod.com',
                'phone' => '081234567893',
                'password' => Hash::make('password'),
                'role_id' => $customerRole->id,
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'status' => 'active',
                'two_factor_enabled' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'API',
                'email' => 'api@mimod.com',
                'phone' => '081234567894',
                'password' => Hash::make('api-secure-password-2024'),
                'role_id' => $adminRole->id,
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'status' => 'active',
                'two_factor_enabled' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);
    }
}
