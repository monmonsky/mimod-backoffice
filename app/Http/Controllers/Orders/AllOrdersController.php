<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Orders\OrderRepositoryInterface;
use Illuminate\Http\Request;

class AllOrdersController extends Controller
{
    protected $orderRepo;

    public function __construct(OrderRepositoryInterface $orderRepo)
    {
        $this->orderRepo = $orderRepo;
    }

    public function index(Request $request)
    {
        // Get filters
        $filters = $request->only(['order_number', 'customer', 'status', 'date_from']);

        // Get paginated orders with filters
        $orders = $this->orderRepo->getAllWithRelationsPaginated($filters, 15);
        $statistics = $this->orderRepo->getStatistics();

        return view('pages.orders.all-orders.index', compact('orders', 'statistics'));
    }

    public function show($id)
    {
        try {
            $order = $this->orderRepo->findByIdWithRelations($id);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $order = $this->orderRepo->findById($id);
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            $validated = $request->validate([
                'status' => 'nullable|in:pending,processing,shipped,completed,cancelled',
                'notes' => 'nullable|string',
                'shipping_address' => 'nullable|string',
            ]);

            $this->orderRepo->update($id, $validated);

            logActivity('update', 'order', $id, "Updated order: {$order->order_number}");

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $order = $this->orderRepo->findById($id);
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            // Check if order can be deleted (only pending/cancelled orders)
            if (!in_array($order->status, ['pending', 'cancelled'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete order with status: ' . $order->status
                ], 400);
            }

            $this->orderRepo->delete($id);

            logActivity('delete', 'order', $id, "Deleted order: {$order->order_number}");

            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $order = $this->orderRepo->findById($id);
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            $validated = $request->validate([
                'status' => 'required|in:pending,processing,shipped,completed,cancelled',
            ]);

            $this->orderRepo->updateStatus($id, $validated['status']);

            logActivity('update', 'order', $id, "Changed order status to: {$validated['status']}");

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export()
    {
        try {
            // TODO: Implement export functionality
            return response()->json([
                'success' => true,
                'message' => 'Export functionality coming soon'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export orders: ' . $e->getMessage()
            ], 500);
        }
    }
}
