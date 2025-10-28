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
        // Truncate orders tables first
        DB::statement('SET CONSTRAINTS ALL DEFERRED');
        DB::table('order_items')->delete();
        DB::table('orders')->delete();
        DB::statement('ALTER SEQUENCE orders_id_seq RESTART WITH 1');
        DB::statement('ALTER SEQUENCE order_items_id_seq RESTART WITH 1');

        $this->command->info('Seeding orders...');

        // Get customer IDs (only active customers with status = 'active')
        $customers = DB::table('customers')
            ->where('status', 'active')
            ->pluck('id')
            ->toArray();

        // Get active products with their variants
        $products = DB::table('products')
            ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->where('products.status', 'active')
            ->where('product_variants.stock_quantity', '>', 0)
            ->select(
                'product_variants.id as variant_id',
                'products.id as product_id',
                'products.name as product_name',
                'product_variants.sku',
                'product_variants.size',
                'product_variants.color',
                'product_variants.price',
                'product_variants.stock_quantity'
            )
            ->get()
            ->toArray();

        if (empty($customers)) {
            $this->command->warn('No active customers found. Please run CustomerSeeder first.');
            return;
        }

        if (empty($products)) {
            $this->command->warn('No products with variants found. Please run ProductSeeder first.');
            return;
        }

        $this->command->info('Found ' . count($customers) . ' customers and ' . count($products) . ' product variants.');

        $statuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];
        $paymentMethods = ['bank_transfer', 'credit_card', 'e_wallet', 'cod'];
        $couriers = ['JNE', 'TIKI', 'SiCepat', 'J&T', 'Pos Indonesia'];

        $orders = [];
        $orderItems = [];

        // Create 20 orders (2 orders per customer on average)
        for ($i = 1; $i <= 20; $i++) {
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
                $product = $products[array_rand($products)];
                $quantity = rand(1, min(3, $product->stock_quantity)); // Don't exceed stock
                $price = $product->price;
                $itemSubtotal = $price * $quantity;
                $discount = $j === 0 ? rand(0, 10000) : 0; // First item might have discount
                $itemTotal = $itemSubtotal - $discount;

                $subtotal += $itemTotal;

                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'variant_id' => $product->variant_id,
                    'product_name' => $product->product_name,
                    'sku' => $product->sku,
                    'size' => $product->size,
                    'color' => $product->color,
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
        $this->command->info('- 20 orders created');
        $this->command->info('- ' . DB::table('order_items')->count() . ' order items created');
        $this->command->info('- Status distribution: pending, processing, shipped, completed, cancelled');
        $this->command->info('- Each order has 1-5 items from active products');
    }
}
