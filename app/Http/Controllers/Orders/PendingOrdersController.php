<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Orders\OrderRepositoryInterface;
use Illuminate\Http\Request;

class PendingOrdersController extends Controller
{
    protected $orderRepo;

    public function __construct(OrderRepositoryInterface $orderRepo)
    {
        $this->orderRepo = $orderRepo;
    }

    public function index()
    {
        $orders = $this->orderRepo->getByStatus('pending');
        $statistics = $this->orderRepo->getStatistics();

        return view('pages.orders.pending-orders.index', compact('orders', 'statistics'));
    }

    public function confirm($id)
    {
        try {
            $order = $this->orderRepo->findById($id);
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            if ($order->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending orders can be confirmed'
                ], 400);
            }

            $this->orderRepo->updateStatus($id, 'processing');

            logActivity('update', 'order', $id, "Confirmed order: {$order->order_number}");

            return response()->json([
                'success' => true,
                'message' => 'Order confirmed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancel(Request $request, $id)
    {
        try {
            $order = $this->orderRepo->findById($id);
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            if ($order->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending orders can be cancelled'
                ], 400);
            }

            $validated = $request->validate([
                'cancellation_reason' => 'nullable|string|max:500',
            ]);

            $this->orderRepo->update($id, [
                'status' => 'cancelled',
                'cancellation_reason' => $validated['cancellation_reason'] ?? null,
            ]);

            logActivity('update', 'order', $id, "Cancelled order: {$order->order_number}");

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order: ' . $e->getMessage()
            ], 500);
        }
    }
}
