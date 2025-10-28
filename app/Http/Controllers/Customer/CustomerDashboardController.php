<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerDashboardController extends Controller
{
    protected $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Get customer dashboard overview
     */
    public function index(Request $request)
    {
        try {
            $customer = $request->customer;

            // Get statistics
            $stats = [
                'total_orders' => $customer->total_orders,
                'total_spent' => $customer->total_spent,
                'average_order_value' => $customer->average_order_value,
                'loyalty_points' => $customer->loyalty_points,
                'last_order_at' => $customer->last_order_at,
            ];

            // Get pending orders count
            $stats['pending_orders'] = DB::table('orders')
                ->where('customer_id', $customer->id)
                ->where('status', 'pending')
                ->count();

            // Get processing orders count
            $stats['processing_orders'] = DB::table('orders')
                ->where('customer_id', $customer->id)
                ->where('status', 'processing')
                ->count();

            // Get shipped orders count (on delivery)
            $stats['shipped_orders'] = DB::table('orders')
                ->where('customer_id', $customer->id)
                ->where('status', 'shipped')
                ->count();

            // Get recent orders (last 5)
            $recentOrders = DB::table('orders')
                ->where('customer_id', $customer->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // Get order items for recent orders
            foreach ($recentOrders as $order) {
                $order->items_count = DB::table('order_items')
                    ->where('order_id', $order->id)
                    ->sum('quantity');

                $order->first_item = DB::table('order_items')
                    ->join('products', 'products.id', '=', 'order_items.product_id')
                    ->where('order_items.order_id', $order->id)
                    ->select('products.name', 'products.slug', 'order_items.quantity')
                    ->first();
            }

            // Get wishlist count (if wishlist feature exists)
            $stats['wishlist_count'] = DB::table('wishlists')
                ->where('customer_id', $customer->id)
                ->count();

            // Get addresses count
            $stats['addresses_count'] = DB::table('customer_addresses')
                ->where('customer_id', $customer->id)
                ->count();

            // Get spending by month (last 6 months)
            $spendingByMonth = DB::table('orders')
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('SUM(total) as total_spent'),
                    DB::raw('COUNT(*) as order_count')
                )
                ->where('customer_id', $customer->id)
                ->where('status', '!=', 'cancelled')
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();

            // Get favorite products (most ordered)
            $favoriteProducts = DB::table('order_items')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'products.id', '=', 'order_items.product_id')
                ->select(
                    'products.id',
                    'products.name',
                    'products.slug',
                    DB::raw('SUM(order_items.quantity) as total_ordered'),
                    DB::raw('COUNT(DISTINCT orders.id) as order_count')
                )
                ->where('orders.customer_id', $customer->id)
                ->where('orders.status', '!=', 'cancelled')
                ->groupBy('products.id', 'products.name', 'products.slug')
                ->orderBy('total_ordered', 'desc')
                ->limit(5)
                ->get();

            // Get product images for favorite products
            foreach ($favoriteProducts as $product) {
                $product->primary_image = DB::table('product_images')
                    ->where('product_id', $product->id)
                    ->where('is_primary', true)
                    ->first();
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Dashboard data retrieved successfully.')
                ->setData([
                    'stats' => $stats,
                    'recent_orders' => $recentOrders,
                    'spending_by_month' => $spendingByMonth,
                    'favorite_products' => $favoriteProducts,
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve dashboard data: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get order statistics
     */
    public function orderStats(Request $request)
    {
        try {
            $customer = $request->customer;
            $period = $request->input('period', 'all'); // all, year, month, week

            $query = DB::table('orders')
                ->where('customer_id', $customer->id);

            // Apply period filter
            switch ($period) {
                case 'year':
                    $query->where('created_at', '>=', now()->startOfYear());
                    break;
                case 'month':
                    $query->where('created_at', '>=', now()->startOfMonth());
                    break;
                case 'week':
                    $query->where('created_at', '>=', now()->startOfWeek());
                    break;
            }

            // Get order count by status
            $ordersByStatus = DB::table('orders')
                ->select('status', DB::raw('COUNT(*) as count'))
                ->where('customer_id', $customer->id)
                ->groupBy('status')
                ->get()
                ->keyBy('status');

            // Get total spent by period
            $totalSpent = $query->where('status', '!=', 'cancelled')->sum('total');
            $orderCount = $query->count();
            $averageOrderValue = $orderCount > 0 ? $totalSpent / $orderCount : 0;

            $stats = [
                'period' => $period,
                'total_orders' => $orderCount,
                'total_spent' => $totalSpent,
                'average_order_value' => $averageOrderValue,
                'orders_by_status' => [
                    'pending' => $ordersByStatus->get('pending')->count ?? 0,
                    'processing' => $ordersByStatus->get('processing')->count ?? 0,
                    'shipped' => $ordersByStatus->get('shipped')->count ?? 0,
                    'delivered' => $ordersByStatus->get('delivered')->count ?? 0,
                    'cancelled' => $ordersByStatus->get('cancelled')->count ?? 0,
                ],
            ];

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Order statistics retrieved successfully.')
                ->setData($stats);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve order statistics: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get recent activities
     */
    public function activities(Request $request)
    {
        try {
            $customer = $request->customer;
            $limit = $request->input('limit', 20);

            $activities = [];

            // Get recent orders
            $orders = DB::table('orders')
                ->where('customer_id', $customer->id)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            foreach ($orders as $order) {
                $activities[] = [
                    'type' => 'order',
                    'action' => 'created',
                    'description' => "Order {$order->order_number} created",
                    'status' => $order->status,
                    'amount' => $order->total,
                    'created_at' => $order->created_at,
                ];
            }

            // Get recent loyalty transactions
            $loyaltyTransactions = DB::table('customer_loyalty_transactions')
                ->where('customer_id', $customer->id)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            foreach ($loyaltyTransactions as $transaction) {
                $activities[] = [
                    'type' => 'loyalty',
                    'action' => $transaction->type,
                    'description' => $transaction->description,
                    'points' => $transaction->points,
                    'created_at' => $transaction->created_at,
                ];
            }

            // Sort by created_at
            usort($activities, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });

            // Limit to requested number
            $activities = array_slice($activities, 0, $limit);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Activities retrieved successfully.')
                ->setData([
                    'activities' => $activities,
                    'total' => count($activities),
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve activities: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get notifications (if notification system exists)
     */
    public function notifications(Request $request)
    {
        try {
            $customer = $request->customer;
            $limit = $request->input('limit', 10);

            // This is a placeholder for notification system
            // You can implement actual notification table later
            $notifications = [];

            // Get recent order updates as notifications
            $recentOrders = DB::table('order_status_history')
                ->join('orders', 'orders.id', '=', 'order_status_history.order_id')
                ->where('orders.customer_id', $customer->id)
                ->select(
                    'order_status_history.*',
                    'orders.order_number',
                    'orders.total'
                )
                ->orderBy('order_status_history.created_at', 'desc')
                ->limit($limit)
                ->get();

            foreach ($recentOrders as $history) {
                $notifications[] = [
                    'type' => 'order_update',
                    'title' => 'Order Status Updated',
                    'message' => "Your order {$history->order_number} is now {$history->status}",
                    'data' => [
                        'order_number' => $history->order_number,
                        'status' => $history->status,
                    ],
                    'read' => false, // You can add read status later
                    'created_at' => $history->created_at,
                ];
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Notifications retrieved successfully.')
                ->setData([
                    'notifications' => $notifications,
                    'unread_count' => count(array_filter($notifications, fn($n) => !$n['read'])),
                    'total' => count($notifications),
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve notifications: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
