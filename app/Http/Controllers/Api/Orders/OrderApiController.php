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

            // Update status
            $updateData = ['status' => $validated['status']];

            // Auto-set shipped_at when status changed to shipped
            if ($validated['status'] === 'shipped' && $oldStatus !== 'shipped') {
                $updateData['shipped_at'] = now();
            }

            $this->orderRepo->update($id, $updateData);

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
