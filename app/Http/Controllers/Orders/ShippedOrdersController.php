<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Orders\OrderRepositoryInterface;
use Illuminate\Http\Request;

class ShippedOrdersController extends Controller
{
    protected $orderRepo;

    public function __construct(OrderRepositoryInterface $orderRepo)
    {
        $this->orderRepo = $orderRepo;
    }

    public function index(Request $request)
    {
        // Get filters
        $filters = $request->only(['order_number', 'customer', 'tracking', 'date_from']);
        $filters['status'] = 'shipped';

        // Get paginated orders with filters
        $orders = $this->orderRepo->getAllWithRelationsPaginated($filters, 15);
        $statistics = $this->orderRepo->getStatistics();

        return view('pages.orders.shipped-orders.index', compact('orders', 'statistics'));
    }

    public function complete(Request $request, $id)
    {
        try {
            $order = $this->orderRepo->findById($id);
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            if ($order->status !== 'shipped') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only shipped orders can be completed'
                ], 400);
            }

            $this->orderRepo->update($id, [
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            logActivity('update', 'order', $id, "Completed order: {$order->order_number}");

            return response()->json([
                'success' => true,
                'message' => 'Order completed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete order: ' . $e->getMessage()
            ], 500);
        }
    }
}
