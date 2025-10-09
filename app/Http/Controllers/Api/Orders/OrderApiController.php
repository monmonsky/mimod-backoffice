<?php

namespace App\Http\Controllers\Api\Orders;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Orders\OrderRepositoryInterface;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Http\Responses\GeneralResponse\Response as ResponseBuilder;
use Illuminate\Http\Request;

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
                ->setMessage('Failed to retrieve pending orders count')
                ->setError($e->getMessage());

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
                ->setMessage('Failed to retrieve recent pending orders')
                ->setError($e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get all orders with pagination and filters
     */
    public function index(Request $request)
    {
        try {
            $filters = $request->only(['order_number', 'customer', 'status', 'date_from']);

            $orders = $this->orderRepo->getAllWithRelationsPaginated($filters, $request->get('per_page', 20));
            $statistics = $this->orderRepo->getStatistics();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Orders retrieved successfully')
                ->setData([
                    'orders' => $orders,
                    'statistics' => $statistics
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve orders')
                ->setError($e->getMessage());

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

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Order retrieved successfully')
                ->setData(['order' => $order]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve order')
                ->setError($e->getMessage());

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
                ->setMessage('Failed to update order')
                ->setError($e->getMessage());

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
            ]);

            $this->orderRepo->updateStatus($id, $validated['status']);

            logActivity('update', "Changed order status to: {$validated['status']}", 'Order', $id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Order status updated successfully');

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update order status')
                ->setError($e->getMessage());

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
                ->setMessage('Failed to delete order')
                ->setError($e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
