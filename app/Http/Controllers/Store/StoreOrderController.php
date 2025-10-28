<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class StoreOrderController extends Controller
{
    /**
     * Create order and save to database
     */
    public function createOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'customer_info.name' => 'required|string|max:255',
            'customer_info.phone' => 'required|string|max:20',
            'customer_info.email' => 'nullable|email',
            'customer_info.address' => 'required|string',
            'customer_info.city' => 'required|string|max:100',
            'customer_info.province' => 'required|string|max:100',
            'customer_info.postal_code' => 'required|string|max:10',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'shipping_method_id' => 'nullable|exists:shipping_methods,id',
            'shipping_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'statusCode' => '422',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $items = [];
            $subtotal = 0;
            $errors = [];

            // Validate items and calculate subtotal
            foreach ($request->items as $index => $item) {
                if (isset($item['product_variant_id']) && $item['product_variant_id']) {
                    // Order variant
                    $variant = DB::table('product_variants')
                        ->where('id', $item['product_variant_id'])
                        ->first();

                    if (!$variant) {
                        $errors["items.{$index}.product_variant_id"] = ["Variant not found"];
                        continue;
                    }

                    // Get product
                    $product = DB::table('products')
                        ->where('id', $variant->product_id)
                        ->first();

                    if (!$product) {
                        $errors["items.{$index}.product_id"] = ["Product not found"];
                        continue;
                    }

                    // Check stock - use isset to avoid undefined property
                    $variantStock = $variant->stock_quantity ?? 0;
                    if ($variantStock < $item['quantity']) {
                        $errors["items.{$index}.quantity"] = [
                            "Insufficient stock. Available: {$variantStock}"
                        ];
                        continue;
                    }

                    $price = $variant->price;
                    $itemSubtotal = $price * $item['quantity'];

                    // Get product image (variant images are stored with product_id)
                    $productImage = DB::table('product_images')
                        ->where('product_id', $variant->product_id)
                        ->orderBy('is_primary', 'desc')
                        ->first();

                    $imageUrl = $productImage ? $productImage->url : null;

                    $items[] = [
                        'type' => 'variant',
                        'product_id' => $variant->product_id,
                        'product_variant_id' => $variant->id,
                        'name' => $product->name,
                        'sku' => $variant->sku,
                        'size' => $variant->size ?? null,
                        'color' => $variant->color ?? null,
                        'variant_name' => $variant->name ?? 'Variant',
                        'price' => $price,
                        'quantity' => $item['quantity'],
                        'subtotal' => $itemSubtotal,
                        'image' => $imageUrl,
                    ];

                    $subtotal += $itemSubtotal;
                } else {
                    // Order product
                    $product = DB::table('products')
                        ->where('id', $item['product_id'])
                        ->first();

                    if (!$product) {
                        $errors["items.{$index}.product_id"] = ["Product not found"];
                        continue;
                    }

                    // Check stock - use isset to avoid undefined property
                    $productStock = $product->stock_quantity ?? 0;
                    if ($productStock < $item['quantity']) {
                        $errors["items.{$index}.quantity"] = [
                            "Insufficient stock. Available: {$productStock}"
                        ];
                        continue;
                    }

                    $price = $product->price;
                    $itemSubtotal = $price * $item['quantity'];

                    // Get product image
                    $image = DB::table('product_images')
                        ->where('product_id', $product->id)
                        ->orderBy('is_primary', 'desc')
                        ->first();

                    $items[] = [
                        'type' => 'product',
                        'product_id' => $product->id,
                        'product_variant_id' => null,
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'size' => null,
                        'color' => null,
                        'variant_name' => null,
                        'price' => $price,
                        'quantity' => $item['quantity'],
                        'subtotal' => $itemSubtotal,
                        'image' => $image ? $image->url : null,
                    ];

                    $subtotal += $itemSubtotal;
                }
            }

            if (!empty($errors)) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'statusCode' => '422',
                    'message' => 'Validation failed',
                    'errors' => $errors
                ], 422);
            }

            $total = $subtotal;

            // Find or create customer
            $customer = DB::table('customers')
                ->where('phone', $request->customer_info['phone'])
                ->first();

            if (!$customer) {
                // Generate customer code
                $lastCustomer = DB::table('customers')
                    ->orderBy('id', 'desc')
                    ->first();

                $nextNumber = $lastCustomer ? (intval(substr($lastCustomer->customer_code, 4)) + 1) : 1;
                $customerCode = 'CUST' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

                // Create customer
                $customerId = DB::table('customers')->insertGetId([
                    'customer_code' => $customerCode,
                    'name' => $request->customer_info['name'],
                    'email' => $request->customer_info['email'],
                    'phone' => $request->customer_info['phone'],
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $customerId = $customer->id;
            }

            // Create or update customer address
            $existingAddress = DB::table('customer_addresses')
                ->where('customer_id', $customerId)
                ->where('address_line', $request->customer_info['address'])
                ->first();

            if (!$existingAddress) {
                // Check if customer has any address
                $hasDefaultAddress = DB::table('customer_addresses')
                    ->where('customer_id', $customerId)
                    ->where('is_default', true)
                    ->exists();

                DB::table('customer_addresses')->insert([
                    'customer_id' => $customerId,
                    'label' => 'Home',
                    'recipient_name' => $request->customer_info['name'],
                    'phone' => $request->customer_info['phone'],
                    'address_line' => $request->customer_info['address'],
                    'city' => $request->customer_info['city'],
                    'province' => $request->customer_info['province'],
                    'postal_code' => $request->customer_info['postal_code'],
                    'country' => 'Indonesia',
                    'is_default' => !$hasDefaultAddress, // First address becomes default
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Get payment method details
            $paymentMethod = DB::table('payment_methods')->where('id', $request->payment_method_id)->first();

            if (!$paymentMethod) {
                return response()->json([
                    'status' => false,
                    'statusCode' => '404',
                    'message' => 'Payment method not found'
                ], 404);
            }

            if (!$paymentMethod->is_active) {
                return response()->json([
                    'status' => false,
                    'statusCode' => '400',
                    'message' => 'Payment method is not available'
                ], 400);
            }

            // Calculate payment fee
            $shippingCost = $request->shipping_cost ?? 0;
            $paymentFee = $paymentMethod->fee_fixed + ($subtotal * $paymentMethod->fee_percentage / 100);
            $total = $subtotal + $shippingCost + $paymentFee;

            // Check min/max amount
            if ($paymentMethod->min_amount && $total < $paymentMethod->min_amount) {
                return response()->json([
                    'status' => false,
                    'statusCode' => '400',
                    'message' => "Minimum order amount for {$paymentMethod->name} is Rp " . number_format($paymentMethod->min_amount)
                ], 400);
            }

            if ($paymentMethod->max_amount && $total > $paymentMethod->max_amount) {
                return response()->json([
                    'status' => false,
                    'statusCode' => '400',
                    'message' => "Maximum order amount for {$paymentMethod->name} is Rp " . number_format($paymentMethod->max_amount)
                ], 400);
            }

            // Get shipping method details (if provided)
            $shippingMethod = null;
            if ($request->shipping_method_id) {
                $shippingMethod = DB::table('shipping_methods')->where('id', $request->shipping_method_id)->first();

                if (!$shippingMethod) {
                    return response()->json([
                        'status' => false,
                        'statusCode' => '404',
                        'message' => 'Shipping method not found'
                    ], 404);
                }

                if (!$shippingMethod->is_active) {
                    return response()->json([
                        'status' => false,
                        'statusCode' => '400',
                        'message' => 'Shipping method is not available'
                    ], 400);
                }
            }

            // Generate order number
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            // Create order
            $orderId = DB::table('orders')->insertGetId([
                'order_number' => $orderNumber,
                'customer_id' => $customerId,
                'status' => 'pending',
                'payment_method' => $paymentMethod->name,
                'payment_status' => 'unpaid',
                'subtotal' => $subtotal,
                'discount_amount' => 0,
                'tax_amount' => $paymentFee,
                'shipping_cost' => $shippingCost,
                'total_amount' => $total,
                'notes' => $request->notes,
                'shipping_phone' => $request->customer_info['phone'],
                'shipping_address' => $request->customer_info['address'] . ', ' . $request->customer_info['name'],
                'shipping_city' => $request->customer_info['city'],
                'shipping_province' => $request->customer_info['province'],
                'shipping_postal_code' => $request->customer_info['postal_code'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create order items and update stock
            foreach ($items as $item) {
                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'variant_id' => $item['product_variant_id'],
                    'product_name' => $item['name'],
                    'sku' => $item['sku'],
                    'size' => $item['size'],
                    'color' => $item['color'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'discount_amount' => 0,
                    'total' => $item['subtotal'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update stock
                if ($item['product_variant_id']) {
                    DB::table('product_variants')
                        ->where('id', $item['product_variant_id'])
                        ->decrement('stock_quantity', $item['quantity']);
                } else {
                    DB::table('products')
                        ->where('id', $item['product_id'])
                        ->decrement('stock_quantity', $item['quantity']);
                }
            }

            // Create order status history
            DB::table('order_status_history')->insert([
                'order_id' => $orderId,
                'status' => 'pending',
                'notes' => 'Order created from store',
                'created_at' => now(),
            ]);

            // Create order payment record
            $expiredAt = null;
            if ($paymentMethod->expired_duration) {
                $expiredAt = now()->addMinutes($paymentMethod->expired_duration);
            }

            DB::table('order_payments')->insert([
                'order_id' => $orderId,
                'payment_method_id' => $paymentMethod->id,
                'payment_channel' => null, // Will be filled by payment gateway
                'transaction_id' => null, // Will be filled by payment gateway
                'amount' => $total,
                'fee_amount' => $paymentFee,
                'status' => 'pending',
                'expired_at' => $expiredAt,
                'metadata' => json_encode([
                    'order_number' => $orderNumber,
                    'payment_method_code' => $paymentMethod->code,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create order shipment record (if shipping method provided)
            if ($shippingMethod) {
                DB::table('order_shipments')->insert([
                    'order_id' => $orderId,
                    'shipping_method_id' => $shippingMethod->id,
                    'carrier_name' => $shippingMethod->name,
                    'service_type' => $shippingMethod->type,
                    'cost' => $shippingCost,
                    'status' => 'pending',
                    'recipient_name' => $request->customer_info['name'],
                    'recipient_phone' => $request->customer_info['phone'],
                    'recipient_address' => $request->customer_info['address'],
                    'recipient_city' => $request->customer_info['city'],
                    'recipient_province' => $request->customer_info['province'],
                    'recipient_postal_code' => $request->customer_info['postal_code'],
                    'metadata' => json_encode([
                        'order_number' => $orderNumber,
                        'shipping_method_code' => $shippingMethod->code,
                        'estimated_delivery' => $shippingMethod->estimated_delivery,
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            // Generate WhatsApp message
            $waMessage = $this->generateWhatsAppMessage(
                $request->customer_info,
                $items,
                $subtotal,
                $total,
                $request->notes,
                $orderNumber
            );

            return response()->json([
                'status' => true,
                'statusCode' => '200',
                'message' => 'Order created successfully',
                'data' => [
                    'order_id' => $orderId,
                    'order_number' => $orderNumber,
                    'customer_id' => $customerId,
                    'items' => $items,
                    'customer_info' => $request->customer_info,
                    'subtotal' => $subtotal,
                    'shipping_cost' => $shippingCost,
                    'payment_fee' => $paymentFee,
                    'total' => $total,
                    'payment_method' => [
                        'id' => $paymentMethod->id,
                        'code' => $paymentMethod->code,
                        'name' => $paymentMethod->name,
                        'type' => $paymentMethod->type,
                        'provider' => $paymentMethod->provider,
                        'instructions' => $paymentMethod->instructions,
                        'expired_at' => $expiredAt,
                    ],
                    'shipping_method' => $shippingMethod ? [
                        'id' => $shippingMethod->id,
                        'code' => $shippingMethod->code,
                        'name' => $shippingMethod->name,
                        'type' => $shippingMethod->type,
                        'provider' => $shippingMethod->provider,
                        'estimated_delivery' => $shippingMethod->estimated_delivery,
                    ] : null,
                    'notes' => $request->notes,
                    'whatsapp_message' => $waMessage,
                    'whatsapp_url' => $this->generateWhatsAppUrl($waMessage),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'statusCode' => '500',
                'message' => 'Failed to create order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate WhatsApp message
     */
    private function generateWhatsAppMessage($customerInfo, $items, $subtotal, $total, $notes, $orderNumber)
    {
        $message = "*ORDER BARU*\n\n";
        $message .= "Order: *{$orderNumber}*\n\n";

        // Customer info
        $message .= "👤 *Customer*\n";
        $message .= "Nama: {$customerInfo['name']}\n";
        $message .= "Telepon: {$customerInfo['phone']}\n";
        if (!empty($customerInfo['email'])) {
            $message .= "Email: {$customerInfo['email']}\n";
        }
        $message .= "\n📍 *Alamat Pengiriman*\n";
        $message .= "{$customerInfo['address']}\n";
        $message .= "{$customerInfo['city']}, {$customerInfo['province']} {$customerInfo['postal_code']}\n\n";

        // Items
        $message .= "🛍️ *Pesanan*\n";
        foreach ($items as $index => $item) {
            $no = $index + 1;
            $message .= "{$no}. {$item['name']}";
            if ($item['variant_name']) {
                $message .= " ({$item['variant_name']})";
            }
            $message .= "\n";
            $message .= "   SKU: {$item['sku']}\n";
            $message .= "   Qty: {$item['quantity']} x Rp " . number_format($item['price'], 0, ',', '.') . "\n";
            $message .= "   Subtotal: Rp " . number_format($item['subtotal'], 0, ',', '.') . "\n";
        }

        // Totals
        $message .= "\n💰 *Ringkasan*\n";
        $message .= "Subtotal: Rp " . number_format($subtotal, 0, ',', '.') . "\n";
        $message .= "*TOTAL: Rp " . number_format($total, 0, ',', '.') . "*\n";

        // Notes
        if ($notes) {
            $message .= "\n📝 *Catatan*\n{$notes}\n";
        }

        return $message;
    }

    /**
     * Generate WhatsApp URL
     */
    private function generateWhatsAppUrl($message)
    {
        // Get WhatsApp number from settings (you can adjust this)
        $whatsappNumber = '6281234567890'; // Default, should be from settings

        return 'https://wa.me/' . $whatsappNumber . '?text=' . urlencode($message);
    }
}
