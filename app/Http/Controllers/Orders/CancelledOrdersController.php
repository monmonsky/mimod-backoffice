<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Orders\OrderRepositoryInterface;

class CancelledOrdersController extends Controller
{
    protected $orderRepo;

    public function __construct(OrderRepositoryInterface $orderRepo)
    {
        $this->orderRepo = $orderRepo;
    }

    public function index()
    {
        $orders = $this->orderRepo->getByStatus('cancelled');
        $statistics = $this->orderRepo->getStatistics();

        return view('pages.orders.cancelled-orders.index', compact('orders', 'statistics'));
    }
}
