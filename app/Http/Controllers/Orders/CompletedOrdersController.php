<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Orders\OrderRepositoryInterface;

class CompletedOrdersController extends Controller
{
    protected $orderRepo;

    public function __construct(OrderRepositoryInterface $orderRepo)
    {
        $this->orderRepo = $orderRepo;
    }

    public function index()
    {
        $orders = $this->orderRepo->getByStatus('completed');
        $statistics = $this->orderRepo->getStatistics();

        return view('pages.orders.completed-orders.index', compact('orders', 'statistics'));
    }
}
