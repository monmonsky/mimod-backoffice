<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Orders\OrderRepositoryInterface;
use Illuminate\Http\Request;

class CompletedOrdersController extends Controller
{
    protected $orderRepo;

    public function __construct(OrderRepositoryInterface $orderRepo)
    {
        $this->orderRepo = $orderRepo;
    }

    public function index(Request $request)
    {
        return view('pages.orders.completed-orders.index');
    }
}
