<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Orders\OrderRepositoryInterface;
use Illuminate\Http\Request;

class ProcessingOrdersController extends Controller
{
    protected $orderRepo;

    public function __construct(OrderRepositoryInterface $orderRepo)
    {
        $this->orderRepo = $orderRepo;
    }

    public function index()
    {
        $orders = $this->orderRepo->getByStatus('processing');
        $statistics = $this->orderRepo->getStatistics();

        return view('pages.orders.processing-orders.index', compact('orders', 'statistics'));
    }

    public function ship(Request $request, $id)
    {
        try {
            $order = $this->orderRepo->findById($id);
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            if ($order->status !== 'processing') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only processing orders can be shipped'
                ], 400);
            }

            $validated = $request->validate([
                'tracking_number' => 'required|string|max:100',
                'courier' => 'required|string|max:100',
                'shipping_notes' => 'nullable|string|max:500',
            ]);

            $this->orderRepo->update($id, [
                'status' => 'shipped',
                'tracking_number' => $validated['tracking_number'],
                'courier' => $validated['courier'],
                'shipping_notes' => $validated['shipping_notes'] ?? null,
                'shipped_at' => now(),
            ]);

            logActivity('update', 'order', $id, "Shipped order: {$order->order_number}");

            return response()->json([
                'success' => true,
                'message' => 'Order shipped successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to ship order: ' . $e->getMessage()
            ], 500);
        }
    }
}
