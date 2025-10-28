<?php

namespace App\Http\Controllers\Api\Orders;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Orders\OrderRepositoryInterface;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Http\Responses\GeneralResponse\Response as ResponseBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderApiController extends Controller
{
    protected $orderRepo;
    protected $response;

    public function __construct(
        OrderRepositoryInterface $orderRepo,
        ResponseBuilder $response
    ) {
        $this->orderRepo = $orderRepo;
        $this->response = $response;
    }

    /**
     * Get pending orders count for notifications
     */
    public function pendingCount()
    {
        try {
            $count = $this->orderRepo->query()
                ->where('status', 'pending')
                ->count();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Pending orders count retrieved successfully')
                ->setData(['count' => $count]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve pending orders count: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get recent pending orders for notification dropdown
     */
    public function recentPending(Request $request)
    {
        try {
            $limit = $request->input('limit', 5);

            $orders = $this->orderRepo->query()
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Recent pending orders retrieved successfully')
                ->setData($orders);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve recent pending orders: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get all orders with pagination and filters
     */
    public function index(Request $request)
    {
        try {
            $filters = $request->only([
                'order_number',
                'customer',
                'status',
                'payment_status',
                'payment_method',
                'tracking',
                'courier',
                'date_from',
                'date_to',
                'min_total',
                'max_total',
                'search',
                'sort_by',
                'sort_order'
            ]);

            $orders = $this->orderRepo->getAllWithRelationsPaginated($filters, $request->get('per_page', 20));
            $statistics = $this->orderRepo->getStatistics();

            // Format orders data to include customer object
            // Convert paginator to array
            $ordersArray = $orders instanceof \Illuminate\Pagination\LengthAwarePaginator
                ? $orders->toArray()
                : (array) $orders;

            // Format each order in the data array
            if (isset($ordersArray['data']) && is_array($ordersArray['data'])) {
                $ordersArray['data'] = array_map(function ($order) {
                    return $this->formatOrderWithCustomer($order);
                }, $ordersArray['data']);
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Orders retrieved successfully')
                ->setData([
                    'orders' => $ordersArray,
                    'statistics' => $statistics
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve orders: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get single order with details
     */
    public function show($id)
    {
        try {
            $order = $this->orderRepo->findByIdWithRelations($id);

            if (!$order) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Order not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Format order with customer object
            $formattedOrder = $this->formatOrderWithCustomer($order);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Order retrieved successfully')
                ->setData($formattedOrder);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve order: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Create new order
     */
    public function store(Request $request)
    {
        try {
            // Validation
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'items' => 'required|array|min:1',
                'items.*.variant_id' => 'required|exists:product_variants,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'shipping_address' => 'required|string',
                'shipping_city' => 'required|string',
                'shipping_province' => 'required|string',
                'shipping_postal_code' => 'nullable|string',
                'shipping_phone' => 'required|string',
                'payment_method_id' => 'required|exists:payment_methods,id',
                'shipping_method_id' => 'nullable|exists:shipping_methods,id',
                'shipping_cost' => 'nullable|numeric|min:0',
                'coupon_code' => 'nullable|string',
                'notes' => 'nullable|string',
                'shipping_notes' => 'nullable|string',
            ]);

            DB::beginTransaction();

            // Get payment method details
            $paymentMethod = DB::table('payment_methods')->where('id', $validated['payment_method_id'])->first();

            if (!$paymentMethod) {
                DB::rollBack();
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Payment method not found')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            if (!$paymentMethod->is_active) {
                DB::rollBack();
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage('Payment method is not available')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 400);
            }

            // Get shipping method details (if provided)
            $shippingMethod = null;
            $shippingCost = $validated['shipping_cost'] ?? 0;

            if (!empty($validated['shipping_method_id'])) {
                $shippingMethod = DB::table('shipping_methods')->where('id', $validated['shipping_method_id'])->first();

                if (!$shippingMethod) {
                    DB::rollBack();
                    $result = (new ResultBuilder())
                        ->setStatus(false)
                        ->setStatusCode('404')
                        ->setMessage('Shipping method not found')
                        ->setData([]);
                    return response()->json($this->response->generateResponse($result), 404);
                }

                if (!$shippingMethod->is_active) {
                    DB::rollBack();
                    $result = (new ResultBuilder())
                        ->setStatus(false)
                        ->setStatusCode('400')
                        ->setMessage('Shipping method is not available')
                        ->setData([]);
                    return response()->json($this->response->generateResponse($result), 400);
                }
            }

            // Generate order number
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            // Calculate subtotal from items
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }

            // Apply coupon discount if provided
            $discountAmount = 0;
            if (!empty($validated['coupon_code'])) {
                // Validate coupon
                $couponRepo = app(\App\Repositories\Contracts\Marketing\CouponRepositoryInterface::class);
                $couponValidation = $couponRepo->validateCoupon(
                    $validated['coupon_code'],
                    $validated['customer_id'],
                    $subtotal
                );

                if ($couponValidation['valid'] ?? false) {
                    $coupon = $couponValidation['coupon'];

                    if ($coupon->type === 'percentage') {
                        $discountAmount = ($subtotal * $coupon->value) / 100;
                        if ($coupon->max_discount && $discountAmount > $coupon->max_discount) {
                            $discountAmount = $coupon->max_discount;
                        }
                    } elseif ($coupon->type === 'fixed') {
                        $discountAmount = $coupon->value;
                    }
                    // free_shipping type doesn't affect subtotal discount
                }
            }

            // Calculate payment fee
            $paymentFee = $paymentMethod->fee_fixed + ($subtotal * $paymentMethod->fee_percentage / 100);

            // Calculate tax (payment fee is treated as tax)
            $taxAmount = $paymentFee;

            // Calculate total
            $totalAmount = $subtotal - $discountAmount + $taxAmount + $shippingCost;

            // Check min/max amount for payment method
            if ($paymentMethod->min_amount && $totalAmount < $paymentMethod->min_amount) {
                DB::rollBack();
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage("Minimum order amount for {$paymentMethod->name} is Rp " . number_format($paymentMethod->min_amount))
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 400);
            }

            if ($paymentMethod->max_amount && $totalAmount > $paymentMethod->max_amount) {
                DB::rollBack();
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage("Maximum order amount for {$paymentMethod->name} is Rp " . number_format($paymentMethod->max_amount))
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 400);
            }

            // Create order
            $orderData = [
                'order_number' => $orderNumber,
                'customer_id' => $validated['customer_id'],
                'user_id' => auth()->id(),
                'status' => 'pending',
                'payment_method' => $paymentMethod->name, // Keep for backward compatibility
                'payment_status' => 'unpaid',
                'shipping_address' => $validated['shipping_address'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_province' => $validated['shipping_province'],
                'shipping_postal_code' => $validated['shipping_postal_code'] ?? null,
                'shipping_phone' => $validated['shipping_phone'],
                'courier' => $shippingMethod->name ?? null, // Keep for backward compatibility
                'shipping_cost' => $shippingCost,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
                'shipping_notes' => $validated['shipping_notes'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $orderId = DB::table('orders')->insertGetId($orderData);

            // Create order items
            foreach ($validated['items'] as $item) {
                // Get variant details
                $variant = DB::table('product_variants as pv')
                    ->join('products as p', 'pv.product_id', '=', 'p.id')
                    ->where('pv.id', $item['variant_id'])
                    ->select('p.name as product_name', 'pv.sku', 'pv.size', 'pv.color')
                    ->first();

                $itemSubtotal = $item['price'] * $item['quantity'];
                $itemDiscount = 0;
                $itemTotal = $itemSubtotal - $itemDiscount;

                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'variant_id' => $item['variant_id'],
                    'product_name' => $variant->product_name ?? 'Unknown Product',
                    'sku' => $variant->sku ?? '',
                    'size' => $variant->size ?? '',
                    'color' => $variant->color ?? '',
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $itemSubtotal,
                    'discount_amount' => $itemDiscount,
                    'total' => $itemTotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update variant stock
                DB::table('product_variants')
                    ->where('id', $item['variant_id'])
                    ->decrement('stock_quantity', $item['quantity']);
            }

            // Record coupon usage if applied
            if (!empty($validated['coupon_code']) && $discountAmount > 0) {
                $couponRepo = app(\App\Repositories\Contracts\Marketing\CouponRepositoryInterface::class);
                $coupon = $couponRepo->findByCode($validated['coupon_code']);
                if ($coupon) {
                    $couponRepo->recordUsage($coupon->id, $validated['customer_id'], $orderId, $discountAmount);
                }
            }

            // Create order status history
            DB::table('order_status_history')->insert([
                'order_id' => $orderId,
                'status' => 'pending',
                'notes' => 'Order created via backoffice',
                'user_id' => auth()->id() ?? null,
                'created_at' => now()
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
                'amount' => $totalAmount,
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
                    'courier_code' => $shippingMethod->code,
                    'service_code' => $shippingMethod->type,
                    'tracking_number' => null,
                    'weight' => 0, // Should be calculated from items
                    'cost' => $shippingCost,
                    'estimated_delivery' => $shippingMethod->estimated_delivery ?? null,
                    'status' => 'pending',
                    'metadata' => json_encode([
                        'order_number' => $orderNumber,
                        'shipping_method_code' => $shippingMethod->code,
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            // Retrieve created order with relations
            $order = $this->orderRepo->findByIdWithRelations($orderId);
            $formattedOrder = $this->formatOrderWithCustomer($order);

            // Log activity
            logActivity('create', "Created order: {$orderNumber}", 'order', $orderId);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('201')
                ->setMessage('Order created successfully')
                ->setData($formattedOrder);

            return response()->json($this->response->generateResponse($result), 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('422')
                ->setMessage($e->validator->errors()->first())
                ->setData(['errors' => $e->validator->errors()]);

            return response()->json($this->response->generateResponse($result), 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Order creation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to create order: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update order
     */
    public function update(Request $request, $id)
    {
        try {
            $order = $this->orderRepo->findById($id);

            if (!$order) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Order not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            $validated = $request->validate([
                'status' => 'nullable|in:pending,processing,shipped,completed,cancelled',
                'notes' => 'nullable|string',
                'shipping_address' => 'nullable|string',
            ]);

            $this->orderRepo->update($id, $validated);

            logActivity('update', "Updated order: {$order->order_number}", 'Order', $id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Order updated successfully');

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update order: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $order = $this->orderRepo->findById($id);

            if (!$order) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Order not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            $validated = $request->validate([
                'status' => 'required|in:pending,processing,shipped,completed,cancelled',
                'notes' => 'nullable|string',
            ]);

            $oldStatus = $order->status;

            DB::beginTransaction();

            // Update status
            $updateData = ['status' => $validated['status']];

            // Auto-set timestamps based on status
            if ($validated['status'] === 'processing' && $oldStatus !== 'processing') {
                $updateData['paid_at'] = now();
            }
            if ($validated['status'] === 'shipped' && $oldStatus !== 'shipped') {
                $updateData['shipped_at'] = now();
            }
            if ($validated['status'] === 'completed' && $oldStatus !== 'completed') {
                $updateData['completed_at'] = now();
            }
            if ($validated['status'] === 'cancelled' && $oldStatus !== 'cancelled') {
                $updateData['cancelled_at'] = now();
            }

            $this->orderRepo->update($id, $updateData);

            // Create order status history
            DB::table('order_status_history')->insert([
                'order_id' => $id,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
                'user_id' => auth()->id() ?? null,
                'created_at' => now()
            ]);

            DB::commit();

            // Log activity
            logActivity('update', "Changed order #{$order->order_number} status from '{$oldStatus}' to '{$validated['status']}'", 'order', (int) $id, [
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Order status updated successfully')
                ->setData([
                    'order_id' => $id,
                    'order_number' => $order->order_number,
                    'old_status' => $oldStatus,
                    'new_status' => $validated['status'],
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('422')
                ->setMessage('Validation failed')
                ->setData(['errors' => $e->errors()]);

            return response()->json($this->response->generateResponse($result), 422);
        } catch (\Exception $e) {
            DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update order status: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, $id)
    {
        try {
            $order = $this->orderRepo->findById($id);

            if (!$order) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Order not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            $validated = $request->validate([
                'payment_status' => 'required|in:paid,unpaid',
                'notes' => 'nullable|string',
            ]);

            $oldPaymentStatus = $order->payment_status;

            // Update payment status
            $updateData = ['payment_status' => $validated['payment_status']];

            // Auto-set paid_at when payment status changed to paid
            if ($validated['payment_status'] === 'paid' && $oldPaymentStatus !== 'paid') {
                $updateData['paid_at'] = now();
            } elseif ($validated['payment_status'] === 'unpaid') {
                $updateData['paid_at'] = null;
            }

            $this->orderRepo->update($id, $updateData);

            // Log activity
            logActivity('update', "Changed order #{$order->order_number} payment status from '{$oldPaymentStatus}' to '{$validated['payment_status']}'", 'order', (int) $id, [
                'old_payment_status' => $oldPaymentStatus,
                'new_payment_status' => $validated['payment_status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Payment status updated successfully')
                ->setData([
                    'order_id' => $id,
                    'order_number' => $order->order_number,
                    'old_payment_status' => $oldPaymentStatus,
                    'new_payment_status' => $validated['payment_status'],
                    'paid_at' => $updateData['paid_at'],
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('422')
                ->setMessage('Validation failed')
                ->setData(['errors' => $e->errors()]);

            return response()->json($this->response->generateResponse($result), 422);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update payment status: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Delete order
     */
    public function destroy($id)
    {
        try {
            $order = $this->orderRepo->findById($id);

            if (!$order) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Order not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Check if order can be deleted (only pending/cancelled orders)
            if (!in_array($order->status, ['pending', 'cancelled'])) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage('Cannot delete order with status: ' . $order->status);

                return response()->json($this->response->generateResponse($result), 400);
            }

            $this->orderRepo->delete($id);

            logActivity('delete', "Deleted order: {$order->order_number}", 'Order', $id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Order deleted successfully');

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to delete order: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get orders by customer ID
     */
    public function byCustomer(Request $request, $customerId)
    {
        try {
            $perPage = $request->input('per_page', 20);
            $status = $request->input('status');

            $query = $this->orderRepo->query()
                ->where('customer_id', $customerId)
                ->orderBy('created_at', 'desc');

            if ($status) {
                $query->where('status', $status);
            }

            $orders = $query->paginate($perPage);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Customer orders retrieved successfully')
                ->setData($orders);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve customer orders: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get order status history
     */
    public function getHistory($id)
    {
        try {
            $order = $this->orderRepo->findById($id);

            if (!$order) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Order not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Get order history with user information
            $history = DB::table('order_status_history')
                ->leftJoin('users', 'order_status_history.user_id', '=', 'users.id')
                ->where('order_status_history.order_id', $id)
                ->select(
                    'order_status_history.*',
                    'users.name as user_name'
                )
                ->orderBy('order_status_history.created_at', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'order_id' => $item->order_id,
                        'status' => $item->status,
                        'notes' => $item->notes,
                        'user_id' => $item->user_id,
                        'user_name' => $item->user_name ?? 'System',
                        'created_at' => $item->created_at
                    ];
                });

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Order history retrieved successfully')
                ->setData($history);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve order history: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get invoice data
     */
    public function getInvoice($id)
    {
        try {
            $order = $this->orderRepo->findByIdWithRelations($id);

            if (!$order) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Order not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Get customer
            $customer = DB::table('customers')->where('id', $order->customer_id)->first();

            // Get store settings
            $storeSettings = DB::table('settings')
                ->whereIn('key', ['store.info', 'store.contact', 'store.address'])
                ->get()
                ->keyBy('key');

            $storeInfo = [
                'name' => json_decode($storeSettings['store.info']->value ?? '{}', true)['name'] ?? 'Minimoda Store',
                'email' => json_decode($storeSettings['store.contact']->value ?? '{}', true)['email'] ?? 'info@minimoda.id',
                'phone' => json_decode($storeSettings['store.contact']->value ?? '{}', true)['phone'] ?? '+62 812 3456 7890',
                'address' => json_decode($storeSettings['store.address']->value ?? '{}', true)['street'] ?? 'Jakarta, Indonesia',
            ];

            // Format invoice data
            $invoiceData = [
                'invoice' => [
                    'invoice_number' => $order->order_number,
                    'invoice_date' => $order->created_at,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status
                ],
                'store' => $storeInfo,
                'customer' => [
                    'name' => $customer->name ?? 'N/A',
                    'email' => $customer->email ?? null,
                    'phone' => $customer->phone ?? $order->shipping_phone,
                    'address' => $order->shipping_address . ', ' . $order->shipping_city . ', ' . $order->shipping_province . ' ' . $order->shipping_postal_code
                ],
                'items' => $order->items ?? [],
                'summary' => [
                    'subtotal' => (int) $order->subtotal,
                    'shipping_cost' => (int) $order->shipping_cost,
                    'tax_amount' => (int) ($order->tax_amount ?? 0),
                    'discount_amount' => (int) ($order->discount_amount ?? 0),
                    'total_amount' => (int) $order->total_amount
                ],
                'payment' => [
                    'method' => $order->payment_method,
                    'status' => $order->payment_status,
                    'paid_at' => $order->paid_at
                ],
                'shipping' => [
                    'courier' => $order->courier,
                    'tracking_number' => $order->tracking_number,
                    'shipped_at' => $order->shipped_at,
                    'address' => $order->shipping_address,
                    'city' => $order->shipping_city,
                    'province' => $order->shipping_province,
                    'postal_code' => $order->shipping_postal_code,
                    'phone' => $order->shipping_phone,
                    'notes' => $order->shipping_notes
                ],
                'notes' => $order->notes
            ];

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Invoice retrieved successfully')
                ->setData($invoiceData);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve invoice: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Send invoice email
     */
    public function sendInvoice(Request $request, $id)
    {
        try {
            $order = $this->orderRepo->findByIdWithRelations($id);

            if (!$order) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Order not found');

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Get customer
            $customer = DB::table('customers')->where('id', $order->customer_id)->first();

            if (!$customer || !$customer->email) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage('Customer email not found');

                return response()->json($this->response->generateResponse($result), 400);
            }

            // Get store info from settings
            $storeSettings = DB::table('settings')
                ->whereIn('key', ['store.info', 'store.contact', 'store.address'])
                ->get()
                ->keyBy('key');

            $storeInfo = [
                'name' => json_decode($storeSettings['store.info']->value ?? '{}', true)['name'] ?? 'Minimoda',
                'email' => json_decode($storeSettings['store.contact']->value ?? '{}', true)['email'] ?? 'info@minimoda.id',
                'phone' => json_decode($storeSettings['store.contact']->value ?? '{}', true)['phone'] ?? '+62 812 3456 7890',
                'address' => json_decode($storeSettings['store.address']->value ?? '{}', true)['street'] ?? 'Jakarta, Indonesia',
            ];

            // Send email
            \Mail::to($customer->email)->send(
                new \App\Mail\OrderInvoice($order, $customer, $order->items ?? [], $storeInfo)
            );

            // Log activity
            logActivity('send_email', "Sent invoice email for order #{$order->order_number} to {$customer->email}", 'order', (int) $id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Invoice email sent successfully')
                ->setData([
                    'order_id' => $id,
                    'order_number' => $order->order_number,
                    'email_to' => $customer->email,
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to send invoice email: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Format order data with customer object
     */
    private function formatOrderWithCustomer($order)
    {
        // Convert to array if it's an object
        $orderArray = is_array($order) ? $order : (array) $order;

        // Extract customer data
        $customer = null;
        if (isset($orderArray['customer_name']) || isset($orderArray['customer_email'])) {
            $customer = [
                'id' => $orderArray['customer_id'] ?? null,
                'name' => $orderArray['customer_name'] ?? null,
                'email' => $orderArray['customer_email'] ?? null,
                'phone' => $orderArray['customer_phone'] ?? null,
            ];

            // Remove customer fields from order
            unset($orderArray['customer_name']);
            unset($orderArray['customer_email']);
            unset($orderArray['customer_phone']);
        }

        // Add customer object to order
        $orderArray['customer'] = $customer;

        return $orderArray;
    }
}
