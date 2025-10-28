<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            [
                'name' => 'Wholesale Customers',
                'code' => 'WHOLESALE',
                'description' => 'Customers who buy in bulk for resale purposes',
                'color' => '#3b82f6',
                'member_count' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Corporate Clients',
                'code' => 'CORPORATE',
                'description' => 'Business customers with corporate accounts',
                'color' => '#8b5cf6',
                'member_count' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Retail Customers',
                'code' => 'RETAIL',
                'description' => 'Regular individual customers',
                'color' => '#10b981',
                'member_count' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'VIP Members',
                'code' => 'VIP',
                'description' => 'Premium customers with special privileges',
                'color' => '#f59e0b',
                'member_count' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Distributor Partners',
                'code' => 'DISTRIBUTOR',
                'description' => 'Authorized distributors of our products',
                'color' => '#ef4444',
                'member_count' => 0,
                'is_active' => true,
            ],
        ];

        foreach ($groups as $group) {
            DB::table('customer_groups')->insert([
                'name' => $group['name'],
                'code' => $group['code'],
                'description' => $group['description'],
                'color' => $group['color'],
                'member_count' => $group['member_count'],
                'is_active' => $group['is_active'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "✓ Created customer group: {$group['name']}\n";
        }

        // Add some customers to groups
        $customers = DB::table('customers')->limit(10)->get();
        $groupIds = DB::table('customer_groups')->pluck('id')->toArray();

        if ($customers->count() > 0 && !empty($groupIds)) {
            foreach ($customers as $index => $customer) {
                // Assign each customer to 1-2 random groups
                $numGroups = rand(1, 2);
                $selectedGroups = array_rand(array_flip($groupIds), min($numGroups, count($groupIds)));

                if (!is_array($selectedGroups)) {
                    $selectedGroups = [$selectedGroups];
                }

                foreach ($selectedGroups as $groupId) {
                    // Check if membership already exists
                    $exists = DB::table('customer_group_members')
                        ->where('customer_group_id', $groupId)
                        ->where('customer_id', $customer->id)
                        ->exists();

                    if (!$exists) {
                        DB::table('customer_group_members')->insert([
                            'customer_group_id' => $groupId,
                            'customer_id' => $customer->id,
                            'joined_at' => now()->subDays(rand(1, 90)),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            // Update member counts
            foreach ($groupIds as $groupId) {
                $count = DB::table('customer_group_members')
                    ->where('customer_group_id', $groupId)
                    ->count();

                DB::table('customer_groups')
                    ->where('id', $groupId)
                    ->update(['member_count' => $count]);
            }

            echo "✓ Assigned customers to groups\n";
        }

        echo "\nCustomer groups seeded successfully!\n";
    }
}
