<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoyaltyProgramsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create loyalty programs
        $programs = [
            [
                'name' => 'Standard Rewards',
                'code' => 'STANDARD',
                'description' => 'Earn 1 point for every Rp 10,000 spent. Redeem 100 points for Rp 10,000 discount.',
                'points_per_currency' => 0.0001, // 1 point per 10,000
                'currency_per_point' => 100, // 100 rupiah per point
                'min_points_redeem' => 100,
                'points_expiry_days' => 365,
                'is_active' => true,
                'start_date' => now()->subMonths(6),
                'end_date' => null,
            ],
            [
                'name' => 'VIP Platinum',
                'code' => 'VIP_PLATINUM',
                'description' => 'Exclusive for VIP members. Earn 2x points and enjoy special redemption rates.',
                'points_per_currency' => 0.0002, // 2 points per 10,000
                'currency_per_point' => 150, // 150 rupiah per point
                'min_points_redeem' => 50,
                'points_expiry_days' => null, // No expiry
                'is_active' => true,
                'start_date' => now()->subMonths(3),
                'end_date' => null,
            ],
            [
                'name' => 'Birthday Special',
                'code' => 'BIRTHDAY',
                'description' => 'Special birthday month promotion with 3x points!',
                'points_per_currency' => 0.0003, // 3 points per 10,000
                'currency_per_point' => 100,
                'min_points_redeem' => 100,
                'points_expiry_days' => 30,
                'is_active' => true,
                'start_date' => now()->startOfMonth(),
                'end_date' => now()->endOfMonth(),
            ],
        ];

        foreach ($programs as $program) {
            DB::table('loyalty_programs')->insert([
                'name' => $program['name'],
                'code' => $program['code'],
                'description' => $program['description'],
                'points_per_currency' => $program['points_per_currency'],
                'currency_per_point' => $program['currency_per_point'],
                'min_points_redeem' => $program['min_points_redeem'],
                'points_expiry_days' => $program['points_expiry_days'],
                'is_active' => $program['is_active'],
                'start_date' => $program['start_date'],
                'end_date' => $program['end_date'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "✓ Created loyalty program: {$program['name']}\n";
        }

        // Create some loyalty transactions
        $customers = DB::table('customers')->limit(15)->get();
        $programId = DB::table('loyalty_programs')->where('code', 'STANDARD')->value('id');

        if ($customers->count() > 0 && $programId) {
            foreach ($customers as $customer) {
                $balance = 0;

                // Earn transactions (3-5 per customer)
                $earnCount = rand(3, 5);
                for ($i = 0; $i < $earnCount; $i++) {
                    $points = rand(50, 500);
                    $balance += $points;

                    DB::table('loyalty_transactions')->insert([
                        'customer_id' => $customer->id,
                        'loyalty_program_id' => $programId,
                        'order_id' => null,
                        'transaction_type' => 'earn',
                        'points' => $points,
                        'balance_after' => $balance,
                        'description' => 'Points earned from purchase',
                        'reference_type' => 'order',
                        'reference_id' => rand(1, 100),
                        'expires_at' => now()->addYear(),
                        'created_at' => now()->subDays(rand(1, 90)),
                        'updated_at' => now(),
                    ]);
                }

                // Some customers redeem points
                if (rand(0, 1) && $balance >= 100) {
                    $redeemPoints = min($balance, rand(100, 300));
                    $balance -= $redeemPoints;

                    DB::table('loyalty_transactions')->insert([
                        'customer_id' => $customer->id,
                        'loyalty_program_id' => $programId,
                        'order_id' => null,
                        'transaction_type' => 'redeem',
                        'points' => -$redeemPoints,
                        'balance_after' => $balance,
                        'description' => 'Points redeemed for discount',
                        'reference_type' => 'order',
                        'reference_id' => rand(1, 100),
                        'expires_at' => null,
                        'created_at' => now()->subDays(rand(1, 30)),
                        'updated_at' => now(),
                    ]);
                }

                // Update customer loyalty points
                DB::table('customers')
                    ->where('id', $customer->id)
                    ->update(['loyalty_points' => $balance]);
            }

            echo "✓ Created loyalty transactions for {$customers->count()} customers\n";
        }

        echo "\nLoyalty programs seeded successfully!\n";
    }
}
