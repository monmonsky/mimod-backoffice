<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Orders\OrderRepositoryInterface;
use Illuminate\Http\Request;

class CancelledOrdersController extends Controller
{
    protected $orderRepo;

    public function __construct(OrderRepositoryInterface $orderRepo)
    {
        $this->orderRepo = $orderRepo;
    }

    public function index(Request $request)
    {
        // Get filters
        $filters = $request->only(['order_number', 'customer', 'date_from']);
        $filters['status'] = 'cancelled';

        // Get paginated orders with filters
        $orders = $this->orderRepo->getAllWithRelationsPaginated($filters, 15);
        $statistics = $this->orderRepo->getStatistics();

        return view('pages.orders.cancelled-orders.index', compact('orders', 'statistics'));
    }
}
