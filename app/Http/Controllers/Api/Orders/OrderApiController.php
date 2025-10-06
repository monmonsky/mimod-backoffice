<?php

namespace App\Http\Controllers\Api\Orders;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Orders\OrderRepositoryInterface;
use App\Utils\ResultBuilder;
use App\Utils\ResponseBuilder;
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
}
