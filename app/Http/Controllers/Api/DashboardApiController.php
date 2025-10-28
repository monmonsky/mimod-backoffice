<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardApiController extends Controller
{
    protected $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Get comprehensive dashboard statistics
     * GET /api/dashboard/statistics
     */
    public function statistics(Request $request)
    {
        try {
            $period = $request->input('period', 'today'); // today, week, month, year, all

            // Date ranges
            $dateRanges = $this->getDateRanges($period);

            $stats = [
                // Orders Statistics
                'orders' => [
                    'total' => DB::table('orders')->count(),
                    'pending' => DB::table('orders')->where('status', 'pending')->count(),
                    'processing' => DB::table('orders')->where('status', 'processing')->count(),
                    'shipped' => DB::table('orders')->where('status', 'shipped')->count(),
                    'completed' => DB::table('orders')->where('status', 'completed')->count(),
                    'cancelled' => DB::table('orders')->where('status', 'cancelled')->count(),
                    'today' => DB::table('orders')->whereDate('created_at', today())->count(),
                    'this_week' => DB::table('orders')
                        ->whereBetween('created_at', $dateRanges['week'])
                        ->count(),
                    'this_month' => DB::table('orders')
                        ->whereBetween('created_at', $dateRanges['month'])
                        ->count(),
                ],

                // Revenue Statistics
                'revenue' => [
                    'total' => (float) DB::table('orders')
                        ->where('payment_status', 'paid')
                        ->sum('total_amount'),
                    'today' => (float) DB::table('orders')
                        ->where('payment_status', 'paid')
                        ->whereDate('created_at', today())
                        ->sum('total_amount'),
                    'this_week' => (float) DB::table('orders')
                        ->where('payment_status', 'paid')
                        ->whereBetween('created_at', $dateRanges['week'])
                        ->sum('total_amount'),
                    'this_month' => (float) DB::table('orders')
                        ->where('payment_status', 'paid')
                        ->whereBetween('created_at', $dateRanges['month'])
                        ->sum('total_amount'),
                    'this_year' => (float) DB::table('orders')
                        ->where('payment_status', 'paid')
                        ->whereBetween('created_at', $dateRanges['year'])
                        ->sum('total_amount'),
                ],

                // Payment Statistics
                'payments' => [
                    'paid' => DB::table('orders')->where('payment_status', 'paid')->count(),
                    'unpaid' => DB::table('orders')->where('payment_status', 'unpaid')->count(),
                    'paid_amount' => (float) DB::table('orders')
                        ->where('payment_status', 'paid')
                        ->sum('total_amount'),
                    'unpaid_amount' => (float) DB::table('orders')
                        ->where('payment_status', 'unpaid')
                        ->sum('total_amount'),
                ],

                // Products Statistics
                'products' => [
                    'total' => DB::table('products')->count(),
                    'active' => DB::table('products')->where('status', 'active')->count(),
                    'inactive' => DB::table('products')->where('status', 'inactive')->count(),
                    'total_variants' => DB::table('product_variants')->count(),
                    'low_stock' => DB::table('product_variants')
                        ->where('stock_quantity', '>', 0)
                        ->where('stock_quantity', '<=', 10)
                        ->count(),
                    'out_of_stock' => DB::table('product_variants')
                        ->where('stock_quantity', '<=', 0)
                        ->count(),
                ],

                // Customers Statistics
                'customers' => [
                    'total' => DB::table('customers')->count(),
                    'new_today' => DB::table('customers')
                        ->whereDate('created_at', today())
                        ->count(),
                    'new_this_week' => DB::table('customers')
                        ->whereBetween('created_at', $dateRanges['week'])
                        ->count(),
                    'new_this_month' => DB::table('customers')
                        ->whereBetween('created_at', $dateRanges['month'])
                        ->count(),
                ],

                // Top Payment Methods
                'top_payment_methods' => DB::table('orders')
                    ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
                    ->where('payment_status', 'paid')
                    ->groupBy('payment_method')
                    ->orderBy('count', 'desc')
                    ->limit(5)
                    ->get()
                    ->map(function($item) {
                        return [
                            'method' => $item->payment_method,
                            'count' => (int) $item->count,
                            'total' => (float) $item->total,
                        ];
                    }),

                // Top Shipping Methods
                'top_shipping_methods' => DB::table('orders')
                    ->select('courier', DB::raw('COUNT(*) as count'))
                    ->whereNotNull('courier')
                    ->groupBy('courier')
                    ->orderBy('count', 'desc')
                    ->limit(5)
                    ->get()
                    ->map(function($item) {
                        return [
                            'courier' => $item->courier,
                            'count' => (int) $item->count,
                        ];
                    }),
            ];

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Dashboard statistics retrieved successfully')
                ->setData($stats);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve dashboard statistics: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get sales chart data
     * GET /api/dashboard/sales-chart?period=week
     */
    public function salesChart(Request $request)
    {
        try {
            $period = $request->input('period', 'week'); // week, month, year
            $data = [];

            switch ($period) {
                case 'week':
                    // Last 7 days
                    for ($i = 6; $i >= 0; $i--) {
                        $date = now()->subDays($i);
                        $sales = DB::table('orders')
                            ->whereDate('created_at', $date->format('Y-m-d'))
                            ->where('payment_status', 'paid')
                            ->sum('total_amount');

                        $orders = DB::table('orders')
                            ->whereDate('created_at', $date->format('Y-m-d'))
                            ->count();

                        $data[] = [
                            'date' => $date->format('Y-m-d'),
                            'label' => $date->format('D, d M'),
                            'sales' => (float) ($sales ?? 0),
                            'orders' => (int) $orders,
                        ];
                    }
                    break;

                case 'month':
                    // Last 30 days
                    for ($i = 29; $i >= 0; $i--) {
                        $date = now()->subDays($i);
                        $sales = DB::table('orders')
                            ->whereDate('created_at', $date->format('Y-m-d'))
                            ->where('payment_status', 'paid')
                            ->sum('total_amount');

                        $orders = DB::table('orders')
                            ->whereDate('created_at', $date->format('Y-m-d'))
                            ->count();

                        $data[] = [
                            'date' => $date->format('Y-m-d'),
                            'label' => $date->format('d M'),
                            'sales' => (float) ($sales ?? 0),
                            'orders' => (int) $orders,
                        ];
                    }
                    break;

                case 'year':
                    // Last 12 months
                    for ($i = 11; $i >= 0; $i--) {
                        $monthStart = now()->subMonths($i)->startOfMonth();
                        $monthEnd = now()->subMonths($i)->endOfMonth();

                        $sales = DB::table('orders')
                            ->whereBetween('created_at', [$monthStart, $monthEnd])
                            ->where('payment_status', 'paid')
                            ->sum('total_amount');

                        $orders = DB::table('orders')
                            ->whereBetween('created_at', [$monthStart, $monthEnd])
                            ->count();

                        $data[] = [
                            'month' => $monthStart->format('Y-m'),
                            'label' => $monthStart->format('M Y'),
                            'sales' => (float) ($sales ?? 0),
                            'orders' => (int) $orders,
                        ];
                    }
                    break;
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Sales chart data retrieved successfully')
                ->setData([
                    'period' => $period,
                    'chart_data' => $data,
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve sales chart: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get recent orders
     * GET /api/dashboard/recent-orders?limit=10
     */
    public function recentOrders(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);

            $orders = DB::table('orders')
                ->join('customers', 'customers.id', '=', 'orders.customer_id')
                ->select(
                    'orders.id',
                    'orders.order_number',
                    'orders.status',
                    'orders.payment_status',
                    'orders.payment_method',
                    'orders.total_amount',
                    'orders.created_at',
                    'customers.id as customer_id',
                    'customers.name as customer_name',
                    'customers.email as customer_email',
                    'customers.phone as customer_phone'
                )
                ->orderBy('orders.created_at', 'desc')
                ->limit($limit)
                ->get();

            // Get order items count for each order
            $ordersWithItems = $orders->map(function($order) {
                $itemsCount = DB::table('order_items')
                    ->where('order_id', $order->id)
                    ->sum('quantity');

                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'payment_method' => $order->payment_method,
                    'total_amount' => (float) $order->total_amount,
                    'items_count' => (int) $itemsCount,
                    'customer' => [
                        'id' => $order->customer_id,
                        'name' => $order->customer_name,
                        'email' => $order->customer_email,
                        'phone' => $order->customer_phone,
                    ],
                    'created_at' => $order->created_at,
                ];
            });

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Recent orders retrieved successfully')
                ->setData($ordersWithItems);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve recent orders: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get top selling products
     * GET /api/dashboard/top-products?period=month&limit=10
     */
    public function topProducts(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            $period = $request->input('period', 'month'); // week, month, year, all

            $query = DB::table('order_items')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->join('product_variants', 'product_variants.id', '=', 'order_items.variant_id')
                ->join('products', 'products.id', '=', 'product_variants.product_id')
                ->select(
                    'products.id',
                    'products.name',
                    'products.slug',
                    DB::raw('SUM(order_items.quantity) as total_sold'),
                    DB::raw('SUM(order_items.total) as total_revenue'),
                    DB::raw('COUNT(DISTINCT orders.id) as order_count')
                )
                ->where('orders.payment_status', 'paid')
                ->groupBy('products.id', 'products.name', 'products.slug')
                ->orderBy('total_sold', 'desc');

            // Apply period filter
            $dateRanges = $this->getDateRanges($period);
            if ($period !== 'all') {
                $query->whereBetween('orders.created_at', $dateRanges[$period]);
            }

            $products = $query->limit($limit)->get();

            // Get product primary image
            $productsWithImages = $products->map(function($product) {
                $primaryImage = DB::table('product_images')
                    ->where('product_id', $product->id)
                    ->where('is_primary', true)
                    ->value('url');

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'total_sold' => (int) $product->total_sold,
                    'total_revenue' => (float) $product->total_revenue,
                    'order_count' => (int) $product->order_count,
                    'image' => $primaryImage,
                ];
            });

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Top products retrieved successfully')
                ->setData([
                    'period' => $period,
                    'products' => $productsWithImages,
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve top products: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get date ranges for filtering
     */
    private function getDateRanges($period)
    {
        $now = now();

        return [
            'today' => [
                $now->copy()->startOfDay(),
                $now->copy()->endOfDay(),
            ],
            'week' => [
                $now->copy()->startOfWeek(),
                $now->copy()->endOfWeek(),
            ],
            'month' => [
                $now->copy()->startOfMonth(),
                $now->copy()->endOfMonth(),
            ],
            'year' => [
                $now->copy()->startOfYear(),
                $now->copy()->endOfYear(),
            ],
        ];
    }
}
