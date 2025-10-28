<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerOrderController extends Controller
{
    protected $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Get all customer orders
     */
    public function index(Request $request)
    {
        try {
            $customer = $request->customer;

            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);
            $status = $request->input('status'); // pending, processing, shipped, delivered, cancelled

            $query = DB::table('orders')
                ->where('customer_id', $customer->id);

            if ($status) {
                $query->where('status', $status);
            }

            // Get total count
            $total = $query->count();

            // Get paginated orders
            $orders = $query
                ->orderBy('created_at', 'desc')
                ->offset(($page - 1) * $perPage)
                ->limit($perPage)
                ->get();

            // Get order items for each order
            foreach ($orders as $order) {
                $order->items = DB::table('order_items')
                    ->where('order_id', $order->id)
                    ->get();

                // Get product details for each item
                foreach ($order->items as $item) {
                    $item->product = DB::table('products')
                        ->where('id', $item->product_id)
                        ->select('id', 'name', 'slug')
                        ->first();

                    if ($item->variant_id) {
                        $item->variant = DB::table('product_variants')
                            ->where('id', $item->variant_id)
                            ->select('id', 'sku', 'size', 'color')
                            ->first();
                    }
                }
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Orders retrieved successfully.')
                ->setData([
                    'orders' => $orders,
                    'pagination' => [
                        'current_page' => $page,
                        'per_page' => $perPage,
                        'total' => $total,
                        'last_page' => ceil($total / $perPage),
                    ]
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve orders: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get single order detail
     */
    public function show(Request $request, $id)
    {
        try {
            $customer = $request->customer;

            $order = DB::table('orders')
                ->where('id', $id)
                ->where('customer_id', $customer->id)
                ->first();

            if (!$order) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Order not found.')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Get order items
            $order->items = DB::table('order_items')
                ->where('order_id', $order->id)
                ->get();

            // Get product details for each item
            foreach ($order->items as $item) {
                $item->product = DB::table('products')
                    ->where('id', $item->product_id)
                    ->first();

                if ($item->variant_id) {
                    $item->variant = DB::table('product_variants')
                        ->where('id', $item->variant_id)
                        ->first();

                    // Get variant images
                    $item->variant->images = DB::table('product_variant_images')
                        ->where('variant_id', $item->variant_id)
                        ->orderBy('is_primary', 'desc')
                        ->get();
                } else {
                    // Get product images
                    $item->product->images = DB::table('product_images')
                        ->where('product_id', $item->product_id)
                        ->orderBy('is_primary', 'desc')
                        ->get();
                }
            }

            // Get shipping address
            if ($order->shipping_address_id) {
                $order->shipping_address = DB::table('customer_addresses')
                    ->where('id', $order->shipping_address_id)
                    ->first();
            }

            // Get order status history (if exists)
            $order->status_history = DB::table('order_status_history')
                ->where('order_id', $order->id)
                ->orderBy('created_at', 'asc')
                ->get();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Order retrieved successfully.')
                ->setData($order);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve order: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Create new order (checkout)
     */
    public function store(Request $request)
    {
        try {
            $customer = $request->customer;

            $validator = Validator::make($request->all(), [
                'items' => 'required|array|min:1',
                'items.*.variant_id' => 'required|integer|exists:product_variants,id',
                'items.*.quantity' => 'required|integer|min:1',
                'shipping_address_id' => 'required|integer|exists:customer_addresses,id',
                'payment_method' => 'required|string|in:bank_transfer,cod,ewallet,credit_card',
                'shipping_method' => 'required|string',
                'shipping_cost' => 'required|numeric|min:0',
                'notes' => 'nullable|string',
                'coupon_code' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Validation failed')
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            DB::beginTransaction();

            try {
                // Calculate totals
                $subtotal = 0;
                $orderItems = [];

                foreach ($request->items as $item) {
                    $variant = DB::table('product_variants')->where('id', $item['variant_id'])->first();
                    $product = DB::table('products')->where('id', $variant->product_id)->first();

                    if (!$variant || !$product) {
                        throw new \Exception('Product or variant not found');
                    }

                    // Check stock
                    if ($variant->stock_quantity < $item['quantity']) {
                        throw new \Exception("Insufficient stock for {$product->name}");
                    }

                    $itemTotal = $variant->price * $item['quantity'];
                    $subtotal += $itemTotal;

                    $orderItems[] = [
                        'product_id' => $product->id,
                        'variant_id' => $variant->id,
                        'quantity' => $item['quantity'],
                        'price' => $variant->price,
                        'total' => $itemTotal,
                    ];
                }

                // Apply coupon if provided
                $discount = 0;
                $couponId = null;
                if ($request->coupon_code) {
                    $coupon = DB::table('coupons')
                        ->where('code', $request->coupon_code)
                        ->where('is_active', true)
                        ->where('start_date', '<=', now())
                        ->where('end_date', '>=', now())
                        ->first();

                    if ($coupon) {
                        if ($coupon->discount_type === 'percentage') {
                            $discount = ($subtotal * $coupon->discount_value) / 100;
                            if ($coupon->max_discount && $discount > $coupon->max_discount) {
                                $discount = $coupon->max_discount;
                            }
                        } else {
                            $discount = $coupon->discount_value;
                        }
                        $couponId = $coupon->id;
                    }
                }

                $total = $subtotal + $request->shipping_cost - $discount;

                // Generate order number
                $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(DB::table('orders')->count() + 1, 6, '0', STR_PAD_LEFT);

                // Create order
                $orderId = DB::table('orders')->insertGetId([
                    'order_number' => $orderNumber,
                    'customer_id' => $customer->id,
                    'shipping_address_id' => $request->shipping_address_id,
                    'payment_method' => $request->payment_method,
                    'payment_status' => 'pending',
                    'status' => 'pending',
                    'subtotal' => $subtotal,
                    'shipping_cost' => $request->shipping_cost,
                    'discount' => $discount,
                    'total' => $total,
                    'notes' => $request->notes,
                    'coupon_id' => $couponId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Create order items and update stock
                foreach ($orderItems as $item) {
                    $item['order_id'] = $orderId;
                    $item['created_at'] = now();
                    $item['updated_at'] = now();

                    DB::table('order_items')->insert($item);

                    // Update stock
                    DB::table('product_variants')
                        ->where('id', $item['variant_id'])
                        ->decrement('stock_quantity', $item['quantity']);
                }

                // Create order status history
                DB::table('order_status_history')->insert([
                    'order_id' => $orderId,
                    'status' => 'pending',
                    'notes' => 'Order created',
                    'created_at' => now(),
                ]);

                // Update customer stats
                DB::table('customers')
                    ->where('id', $customer->id)
                    ->increment('total_orders');

                // Add loyalty points (1 point per 10,000 spent)
                $loyaltyPoints = floor($total / 10000);
                if ($loyaltyPoints > 0) {
                    DB::table('customers')
                        ->where('id', $customer->id)
                        ->increment('loyalty_points', $loyaltyPoints);

                    DB::table('customer_loyalty_transactions')->insert([
                        'customer_id' => $customer->id,
                        'points' => $loyaltyPoints,
                        'type' => 'earned',
                        'description' => "Earned from order {$orderNumber}",
                        'order_id' => $orderId,
                        'created_at' => now(),
                    ]);
                }

                DB::commit();

                // Get created order
                $order = DB::table('orders')->where('id', $orderId)->first();
                $order->items = DB::table('order_items')->where('order_id', $orderId)->get();

                $result = (new ResultBuilder())
                    ->setStatus(true)
                    ->setStatusCode('201')
                    ->setMessage('Order created successfully.')
                    ->setData($order);

                return response()->json($this->response->generateResponse($result), 201);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to create order: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Cancel order (only if status is pending)
     */
    public function cancel(Request $request, $id)
    {
        try {
            $customer = $request->customer;

            $order = DB::table('orders')
                ->where('id', $id)
                ->where('customer_id', $customer->id)
                ->first();

            if (!$order) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Order not found.')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Only pending orders can be cancelled by customer
            if ($order->status !== 'pending') {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage('Only pending orders can be cancelled.')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 400);
            }

            DB::beginTransaction();

            try {
                // Update order status
                DB::table('orders')
                    ->where('id', $id)
                    ->update([
                        'status' => 'cancelled',
                        'updated_at' => now(),
                    ]);

                // Restore stock
                $orderItems = DB::table('order_items')->where('order_id', $id)->get();
                foreach ($orderItems as $item) {
                    DB::table('product_variants')
                        ->where('id', $item->variant_id)
                        ->increment('stock_quantity', $item->quantity);
                }

                // Add status history
                DB::table('order_status_history')->insert([
                    'order_id' => $id,
                    'status' => 'cancelled',
                    'notes' => 'Cancelled by customer',
                    'created_at' => now(),
                ]);

                DB::commit();

                $result = (new ResultBuilder())
                    ->setStatus(true)
                    ->setStatusCode('200')
                    ->setMessage('Order cancelled successfully.')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 200);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to cancel order: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Track order
     */
    public function track(Request $request, $orderNumber)
    {
        try {
            $customer = $request->customer;

            $order = DB::table('orders')
                ->where('order_number', $orderNumber)
                ->where('customer_id', $customer->id)
                ->first();

            if (!$order) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Order not found.')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Get status history
            $statusHistory = DB::table('order_status_history')
                ->where('order_id', $order->id)
                ->orderBy('created_at', 'asc')
                ->get();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Order tracking retrieved successfully.')
                ->setData([
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'created_at' => $order->created_at,
                    'tracking_history' => $statusHistory,
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to track order: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
