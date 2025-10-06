<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Customers\CustomerRepositoryInterface;
use Illuminate\Http\Request;

class VipCustomersController extends Controller
{
    protected $customerRepo;

    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepo = $customerRepository;
    }

    public function index()
    {
        $customers = $this->customerRepo->getVipCustomers();
        $statistics = $this->customerRepo->getStatistics();

        return view('pages.customers.vip-customers.index', compact('customers', 'statistics'));
    }

    public function toggleVip($id)
    {
        try {
            $customer = $this->customerRepo->toggleVipStatus($id);

            return response()->json([
                'success' => true,
                'message' => 'VIP status updated successfully',
                'data' => $customer
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
