<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate tables first (disable foreign key checks for PostgreSQL)
        DB::statement('SET CONSTRAINTS ALL DEFERRED');
        DB::table('customer_addresses')->delete();
        DB::table('customer_segments')->delete();
        DB::table('customers')->delete();

        $this->command->info('Seeding customers...');

        $firstNames = ['Andi', 'Budi', 'Citra', 'Dewi', 'Eko', 'Fitri', 'Gita', 'Hendra', 'Indah', 'Joko',
                      'Kartika', 'Lina', 'Made', 'Nur', 'Omar', 'Putri', 'Qori', 'Rina', 'Siti', 'Taufik',
                      'Umar', 'Vina', 'Wati', 'Yuni', 'Zahra', 'Agus', 'Bambang', 'Cahya', 'Dina', 'Endang'];

        $lastNames = ['Pratama', 'Wibowo', 'Santoso', 'Kusuma', 'Wijaya', 'Lestari', 'Hidayat', 'Permana',
                     'Saputra', 'Nugroho', 'Rahman', 'Fitriani', 'Sari', 'Putra', 'Mahendra', 'Setiawan',
                     'Hakim', 'Saputri', 'Utami', 'Purnomo', 'Susanto', 'Mulyadi', 'Kartika', 'Kurniawan'];

        $cities = ['Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Semarang', 'Makassar', 'Palembang', 'Tangerang',
                  'Depok', 'Bekasi', 'Yogyakarta', 'Malang', 'Bogor', 'Batam', 'Pekanbaru'];

        $provinces = ['DKI Jakarta', 'Jawa Barat', 'Jawa Timur', 'Jawa Tengah', 'Sumatera Utara', 'Sulawesi Selatan',
                     'Sumatera Selatan', 'Banten', 'DI Yogyakarta', 'Kepulauan Riau', 'Riau'];

        $segments = ['regular', 'premium', 'vip'];
        $statuses = ['active', 'inactive'];
        $genders = ['male', 'female'];

        $customers = [];
        for ($i = 1; $i <= 10; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $fullName = $firstName . ' ' . $lastName;
            $email = strtolower(str_replace(' ', '.', $fullName)) . $i . '@example.com';

            $totalOrders = rand(1, 10);
            $totalSpent = rand(500000, 10000000);
            $avgOrderValue = $totalOrders > 0 ? $totalSpent / $totalOrders : 0;

            // Determine segment based on spending
            $segment = 'regular';
            $isVip = false;
            if ($totalSpent > 10000000) {
                $segment = 'vip';
                $isVip = true;
            } elseif ($totalSpent > 5000000) {
                $segment = 'premium';
            }

            $loyaltyPoints = (int)($totalSpent / 10000); // 1 point per 10,000 spent

            $lastOrderDays = rand(1, 365);
            $lastOrderAt = null;
            if ($totalOrders > 0) {
                $lastOrderAt = now()->subDays($lastOrderDays);
            }

            $customers[] = [
                'customer_code' => 'CUST-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'name' => $fullName,
                'email' => $email,
                'phone' => '08' . rand(1000000000, 9999999999),
                'date_of_birth' => now()->subYears(rand(20, 45))->subDays(rand(1, 365))->format('Y-m-d'),
                'gender' => $genders[array_rand($genders)],
                'segment' => $segment,
                'is_vip' => $isVip,
                'loyalty_points' => $loyaltyPoints,
                'total_orders' => $totalOrders,
                'total_spent' => $totalSpent,
                'average_order_value' => $avgOrderValue,
                'last_order_at' => $lastOrderAt,
                'last_login_at' => rand(0, 1) ? now()->subDays(rand(1, 90)) : null,
                'password' => Hash::make('password'),
                'status' => $totalOrders > 0 ? 'active' : ($statuses[array_rand($statuses)]),
                'email_verified_at' => rand(0, 1) ? now()->subDays(rand(1, 365)) : null,
                'preferences' => json_encode([
                    'newsletter' => rand(0, 1) === 1,
                    'sms_notifications' => rand(0, 1) === 1,
                    'email_notifications' => rand(0, 1) === 1,
                ]),
                'notes' => null,
                'created_at' => now()->subDays(rand(1, 365)),
                'updated_at' => now(),
            ];
        }

        DB::table('customers')->insert($customers);

        $this->command->info('Creating customer addresses...');

        // Create 1-3 addresses per customer
        $addressLabels = ['Home', 'Office', 'Parents House', 'Apartment'];
        $streets = ['Jl. Sudirman', 'Jl. Gatot Subroto', 'Jl. Thamrin', 'Jl. Kuningan', 'Jl. Senopati',
                   'Jl. Kemang', 'Jl. Diponegoro', 'Jl. Ahmad Yani', 'Jl. Merdeka', 'Jl. Pahlawan'];

        $insertedCustomers = DB::table('customers')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        foreach ($insertedCustomers as $customer) {
            $addressCount = rand(1, 3);
            for ($j = 0; $j < $addressCount; $j++) {
                $city = $cities[array_rand($cities)];
                $province = $provinces[array_rand($provinces)];

                DB::table('customer_addresses')->insert([
                    'customer_id' => $customer->id,
                    'label' => $addressLabels[$j % count($addressLabels)],
                    'recipient_name' => $customer->name,
                    'phone' => '08' . rand(1000000000, 9999999999),
                    'address_line' => $streets[array_rand($streets)] . ' No. ' . rand(1, 200),
                    'city' => $city,
                    'province' => $province,
                    'postal_code' => rand(10000, 99999),
                    'country' => 'Indonesia',
                    'is_default' => $j === 0, // First address is default
                    'latitude' => -6.2 + (rand(-100, 100) / 1000),
                    'longitude' => 106.8 + (rand(-100, 100) / 1000),
                    'notes' => rand(0, 1) ? 'Dekat ' . ['indomaret', 'alfamart', 'masjid', 'sekolah'][array_rand(['indomaret', 'alfamart', 'masjid', 'sekolah'])] : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Creating customer segments...');

        $segmentData = [
            [
                'name' => 'New Customers',
                'code' => 'NEW',
                'description' => 'Customers who registered in the last 30 days',
                'color' => 'blue',
                'min_orders' => null,
                'max_orders' => 0,
                'min_spent' => null,
                'max_spent' => null,
                'min_loyalty_points' => null,
                'days_since_last_order' => 30,
                'is_active' => true,
                'is_auto_assign' => true,
                'customer_count' => 0,
            ],
            [
                'name' => 'Regular Customers',
                'code' => 'REGULAR',
                'description' => 'Customers with 1-10 orders and less than 5M total spent',
                'color' => 'green',
                'min_orders' => 1,
                'max_orders' => 10,
                'min_spent' => 0,
                'max_spent' => 5000000,
                'min_loyalty_points' => 0,
                'days_since_last_order' => null,
                'is_active' => true,
                'is_auto_assign' => true,
                'customer_count' => 0,
            ],
            [
                'name' => 'Premium Customers',
                'code' => 'PREMIUM',
                'description' => 'Customers with 5-10M total spent',
                'color' => 'purple',
                'min_orders' => 5,
                'max_orders' => null,
                'min_spent' => 5000000,
                'max_spent' => 10000000,
                'min_loyalty_points' => 500,
                'days_since_last_order' => null,
                'is_active' => true,
                'is_auto_assign' => true,
                'customer_count' => 0,
            ],
            [
                'name' => 'VIP Customers',
                'code' => 'VIP',
                'description' => 'High-value customers with over 10M total spent',
                'color' => 'gold',
                'min_orders' => 10,
                'max_orders' => null,
                'min_spent' => 10000000,
                'max_spent' => null,
                'min_loyalty_points' => 1000,
                'days_since_last_order' => null,
                'is_active' => true,
                'is_auto_assign' => true,
                'customer_count' => 0,
            ],
            [
                'name' => 'Inactive Customers',
                'code' => 'INACTIVE',
                'description' => 'Customers with no orders in the last 180 days',
                'color' => 'gray',
                'min_orders' => 1,
                'max_orders' => null,
                'min_spent' => null,
                'max_spent' => null,
                'min_loyalty_points' => null,
                'days_since_last_order' => 180,
                'is_active' => true,
                'is_auto_assign' => true,
                'customer_count' => 0,
            ],
        ];

        foreach ($segmentData as $segment) {
            DB::table('customer_segments')->insert(array_merge($segment, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('Customers seeded successfully.');
        $this->command->info('- 10 customers created');
        $this->command->info('- ' . DB::table('customer_addresses')->count() . ' customer addresses created');
        $this->command->info('- 5 customer segments created');
    }
}
