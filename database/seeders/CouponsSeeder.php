<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouponsSeeder extends Seeder
{
    public function run(): void
    {
        echo "Seeding coupons...\n";

        $coupons = [
            [
                'code' => 'WELCOME10',
                'name' => 'Welcome New Customers',
                'description' => 'Get 10% off your first order!',
                'type' => 'percentage',
                'value' => 10.00,
                'min_purchase' => 100000.00,
                'max_discount' => 50000.00,
                'usage_limit' => 100,
                'usage_limit_per_customer' => 1,
                'usage_count' => 45,
                'start_date' => now()->subDays(30),
                'end_date' => now()->addDays(30),
                'is_active' => true,
            ],
            [
                'code' => 'SAVE50K',
                'name' => 'Fixed Discount 50K',
                'description' => 'Save Rp 50,000 on orders above Rp 500,000',
                'type' => 'fixed',
                'value' => 50000.00,
                'min_purchase' => 500000.00,
                'max_discount' => null,
                'usage_limit' => 50,
                'usage_limit_per_customer' => 2,
                'usage_count' => 23,
                'start_date' => now()->subDays(15),
                'end_date' => now()->addDays(15),
                'is_active' => true,
            ],
            [
                'code' => 'FREESHIP',
                'name' => 'Free Shipping All Orders',
                'description' => 'Free shipping for all orders above Rp 200,000',
                'type' => 'free_shipping',
                'value' => 0.00,
                'min_purchase' => 200000.00,
                'max_discount' => null,
                'usage_limit' => null,
                'usage_limit_per_customer' => 3,
                'usage_count' => 156,
                'start_date' => now()->subDays(60),
                'end_date' => now()->addDays(60),
                'is_active' => true,
            ],
            [
                'code' => 'FLASH25',
                'name' => 'Flash Sale 25% Off',
                'description' => 'Limited time flash sale - 25% off everything!',
                'type' => 'percentage',
                'value' => 25.00,
                'min_purchase' => null,
                'max_discount' => 100000.00,
                'usage_limit' => 200,
                'usage_limit_per_customer' => 1,
                'usage_count' => 189,
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(2),
                'is_active' => true,
            ],
            [
                'code' => 'BIRTHDAY20',
                'name' => 'Birthday Special',
                'description' => 'Happy birthday! Enjoy 20% off',
                'type' => 'percentage',
                'value' => 20.00,
                'min_purchase' => 150000.00,
                'max_discount' => 75000.00,
                'usage_limit' => null,
                'usage_limit_per_customer' => 1,
                'usage_count' => 67,
                'start_date' => now()->subDays(90),
                'end_date' => now()->addDays(90),
                'is_active' => true,
            ],
            [
                'code' => 'EXPIRED50',
                'name' => 'Expired Coupon Example',
                'description' => 'This coupon has expired',
                'type' => 'fixed',
                'value' => 100000.00,
                'min_purchase' => 1000000.00,
                'max_discount' => null,
                'usage_limit' => 10,
                'usage_limit_per_customer' => 1,
                'usage_count' => 8,
                'start_date' => now()->subDays(60),
                'end_date' => now()->subDays(10),
                'is_active' => false,
            ],
            [
                'code' => 'UPCOMING15',
                'name' => 'Upcoming Sale',
                'description' => 'Save 15% - Coming soon!',
                'type' => 'percentage',
                'value' => 15.00,
                'min_purchase' => 250000.00,
                'max_discount' => 80000.00,
                'usage_limit' => 150,
                'usage_limit_per_customer' => 2,
                'usage_count' => 0,
                'start_date' => now()->addDays(5),
                'end_date' => now()->addDays(35),
                'is_active' => true,
            ],
        ];

        foreach ($coupons as $coupon) {
            $exists = DB::table('coupons')->where('code', $coupon['code'])->exists();

            if (!$exists) {
                $coupon['created_at'] = now();
                $coupon['updated_at'] = now();
                DB::table('coupons')->insert($coupon);
                echo "Created coupon: {$coupon['code']}\n";
            } else {
                echo "Coupon already exists: {$coupon['code']}\n";
            }
        }

        echo "Coupons seeded successfully!\n";
    }
}
