<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Customers\LoyaltyProgramRepositoryInterface;
use App\Repositories\Contracts\Customers\LoyaltyTransactionRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerLoyaltyController extends Controller
{
    protected $programRepo;
    protected $transactionRepo;

    public function __construct(
        LoyaltyProgramRepositoryInterface $programRepository,
        LoyaltyTransactionRepositoryInterface $transactionRepository
    ) {
        $this->programRepo = $programRepository;
        $this->transactionRepo = $transactionRepository;
    }

    public function index(Request $request)
    {
        // Get programs
        $programs = $this->programRepo->getAll();

        // Get transactions with customer info
        $query = DB::table('loyalty_transactions as lt')
            ->join('customers as c', 'lt.customer_id', '=', 'c.id')
            ->select('lt.*', 'c.name as customer_name', 'c.email as customer_email');

        // Filter by transaction type
        if ($request->filled('transaction_type')) {
            $query->where('lt.transaction_type', $request->transaction_type);
        }

        // Filter by customer
        if ($request->filled('customer_search')) {
            $search = $request->customer_search;
            $query->where(function($q) use ($search) {
                $q->where('c.name', 'ILIKE', "%{$search}%")
                  ->orWhere('c.email', 'ILIKE', "%{$search}%");
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('lt.created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('lt.created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $transactions = $query->orderBy('lt.created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Get statistics
        $statistics = $this->programRepo->getStatistics();

        return view('pages.customers.customer-loyalty.index', compact('programs', 'transactions', 'statistics'));
    }

}
