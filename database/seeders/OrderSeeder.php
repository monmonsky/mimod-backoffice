<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get customer IDs
        $customers = DB::table('customers')->pluck('id')->toArray();

        // Get product variants for order items
        $variants = DB::table('product_variants')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->select(
                'product_variants.id as variant_id',
                'products.name as product_name',
                'product_variants.sku',
                'product_variants.size',
                'product_variants.color',
                'product_variants.price'
            )
            ->get()
            ->toArray();

        if (empty($customers) || empty($variants)) {
            $this->command->warn('No customers or product variants found. Please run CustomerSeeder and ProductSeeder first.');
            return;
        }

        $statuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];
        $paymentMethods = ['bank_transfer', 'credit_card', 'e_wallet', 'cod'];
        $couriers = ['JNE', 'TIKI', 'SiCepat', 'J&T', 'Pos Indonesia'];

        $orders = [];
        $orderItems = [];

        // Create 50 dummy orders
        for ($i = 1; $i <= 50; $i++) {
            $status = $statuses[array_rand($statuses)];
            $customerId = $customers[array_rand($customers)];
            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT);
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
            $courier = $couriers[array_rand($couriers)];

            // Get customer address for shipping
            $customerAddress = DB::table('customer_addresses')
                ->where('customer_id', $customerId)
                ->where('is_default', true)
                ->first();

            // Random shipping cost
            $shippingCost = rand(10000, 50000);

            // Create order
            $orderId = DB::table('orders')->insertGetId([
                'order_number' => $orderNumber,
                'customer_id' => $customerId,
                'user_id' => null,
                'status' => $status,
                'payment_method' => $paymentMethod,
                'payment_status' => $status === 'completed' ? 'paid' : ($status === 'cancelled' ? 'unpaid' : 'unpaid'),
                'paid_at' => in_array($status, ['completed', 'shipped']) ? now()->subDays(rand(1, 5)) : null,
                'shipping_address' => $customerAddress ? $customerAddress->address_line : 'Jl. Example No. ' . rand(1, 100),
                'shipping_city' => $customerAddress ? $customerAddress->city : 'Jakarta',
                'shipping_province' => $customerAddress ? $customerAddress->province : 'DKI Jakarta',
                'shipping_postal_code' => $customerAddress ? $customerAddress->postal_code : '12' . rand(100, 999),
                'shipping_phone' => $customerAddress ? $customerAddress->phone : '0812' . rand(10000000, 99999999),
                'courier' => $courier,
                'tracking_number' => $status === 'shipped' || $status === 'completed' ? 'TRK' . rand(100000000, 999999999) : null,
                'shipping_cost' => $shippingCost,
                'shipped_at' => in_array($status, ['shipped', 'completed']) ? now()->subDays(rand(1, 3)) : null,
                'subtotal' => 0, // Will be calculated
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 0, // Will be calculated
                'notes' => $i % 3 === 0 ? 'Mohon kirim cepat, untuk kado ulang tahun' : null,
                'shipping_notes' => null,
                'cancellation_reason' => $status === 'cancelled' ? 'Customer request - berubah pikiran' : null,
                'completed_at' => $status === 'completed' ? now()->subDays(rand(0, 2)) : null,
                'cancelled_at' => $status === 'cancelled' ? now()->subDays(rand(1, 5)) : null,
                'created_at' => now()->subDays(rand(0, 30)),
                'updated_at' => now()->subDays(rand(0, 5)),
            ]);

            // Add 1-5 items per order
            $itemCount = rand(1, 5);
            $subtotal = 0;

            for ($j = 0; $j < $itemCount; $j++) {
                $variant = $variants[array_rand($variants)];
                $quantity = rand(1, 3);
                $price = $variant->price;
                $itemSubtotal = $price * $quantity;
                $discount = $j === 0 ? rand(0, 10000) : 0; // First item might have discount
                $itemTotal = $itemSubtotal - $discount;

                $subtotal += $itemTotal;

                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'variant_id' => $variant->variant_id,
                    'product_name' => $variant->product_name,
                    'sku' => $variant->sku,
                    'size' => $variant->size,
                    'color' => $variant->color,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $itemSubtotal,
                    'discount_amount' => $discount,
                    'total' => $itemTotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Update order totals
            $totalAmount = $subtotal + $shippingCost;
            DB::table('orders')->where('id', $orderId)->update([
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount,
            ]);
        }

        $this->command->info('Orders seeded successfully.');
        $this->command->info('- 50 orders created');
        $this->command->info('- Status distribution: pending, processing, shipped, completed, cancelled');
        $this->command->info('- Each order has 1-5 items');
    }
}
