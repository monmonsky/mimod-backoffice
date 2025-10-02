<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activities = [
            [
                'user_id' => 1,
                'action' => 'login',
                'subject_type' => null,
                'subject_id' => null,
                'description' => 'User logged in successfully',
                'properties' => json_encode(['ip' => '127.0.0.1', 'user_agent' => 'Mozilla/5.0']),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'user_id' => 1,
                'action' => 'create',
                'subject_type' => 'User',
                'subject_id' => 2,
                'description' => 'Created new user: John Doe',
                'properties' => json_encode(['email' => 'john@example.com', 'role' => 'Admin']),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(4),
            ],
            [
                'user_id' => 1,
                'action' => 'update',
                'subject_type' => 'Role',
                'subject_id' => 1,
                'description' => 'Updated role permissions for Super Admin',
                'properties' => json_encode([
                    'old_permissions' => ['dashboard.view', 'users.view'],
                    'new_permissions' => ['dashboard.view', 'users.view', 'users.create']
                ]),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'user_id' => 1,
                'action' => 'delete',
                'subject_type' => 'Permission',
                'subject_id' => 15,
                'description' => 'Deleted permission: test.permission',
                'properties' => json_encode(['permission_name' => 'test.permission']),
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'user_id' => 1,
                'action' => 'update',
                'subject_type' => 'Settings',
                'subject_id' => null,
                'description' => 'Updated email settings configuration',
                'properties' => json_encode([
                    'changed_fields' => ['smtp_host', 'smtp_port', 'smtp_encryption']
                ]),
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'user_id' => 1,
                'action' => 'export',
                'subject_type' => 'UserActivity',
                'subject_id' => null,
                'description' => 'Exported user activity logs to CSV',
                'properties' => json_encode(['filters' => ['date_from' => '2025-01-01', 'date_to' => '2025-01-31']]),
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
                'created_at' => now()->subHours(12),
                'updated_at' => now()->subHours(12),
            ],
            [
                'user_id' => 1,
                'action' => 'view',
                'subject_type' => 'Module',
                'subject_id' => 5,
                'description' => 'Viewed module details: Payment Settings',
                'properties' => null,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'created_at' => now()->subHours(6),
                'updated_at' => now()->subHours(6),
            ],
            [
                'user_id' => 1,
                'action' => 'create',
                'subject_type' => 'Module',
                'subject_id' => 10,
                'description' => 'Created new module: User Activities',
                'properties' => json_encode([
                    'name' => 'User Activities',
                    'route' => '/access-control/user-activities',
                    'is_active' => true
                ]),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'created_at' => now()->subHours(3),
                'updated_at' => now()->subHours(3),
            ],
            [
                'user_id' => 1,
                'action' => 'update',
                'subject_type' => 'Settings',
                'subject_id' => null,
                'description' => 'Updated payment method settings',
                'properties' => json_encode([
                    'method' => 'bank_transfer',
                    'action' => 'add_bank',
                    'bank_name' => 'BCA'
                ]),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'created_at' => now()->subHours(1),
                'updated_at' => now()->subHours(1),
            ],
            [
                'user_id' => 1,
                'action' => 'logout',
                'subject_type' => null,
                'subject_id' => null,
                'description' => 'User logged out',
                'properties' => null,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'created_at' => now()->subMinutes(30),
                'updated_at' => now()->subMinutes(30),
            ],
        ];

        DB::table('user_activities')->insert($activities);
    }
}
