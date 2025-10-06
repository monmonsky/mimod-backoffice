<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Customers\CustomerRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AllCustomersController extends Controller
{
    protected $customerRepo;

    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepo = $customerRepository;
    }

    public function index(Request $request)
    {
        $query = $this->customerRepo->query();

        // Name filter
        if ($request->filled('name')) {
            $query->where('name', 'ILIKE', "%{$request->name}%");
        }

        // Email filter
        if ($request->filled('email')) {
            $query->where('email', 'ILIKE', "%{$request->email}%");
        }

        // Phone filter
        if ($request->filled('phone')) {
            $query->where('phone', 'ILIKE', "%{$request->phone}%");
        }

        // Segment filter
        if ($request->filled('segment')) {
            $query->where('segment', $request->segment);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        // Validate sort fields
        $allowedSortFields = ['created_at', 'name', 'email', 'total_orders', 'total_spent'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }

        $customers = $query->orderBy($sortBy, $sortOrder)->paginate(20)->withQueryString();
        $statistics = $this->customerRepo->getStatistics();

        return view('pages.customers.all-customers.index', compact('customers', 'statistics'));
    }

    public function show($id)
    {
        try {
            $customer = $this->customerRepo->findByIdWithAddresses($id);

            return response()->json([
                'success' => true,
                'data' => $customer
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function detail(Request $request, $id)
    {
        // Get customer with addresses
        $customer = $this->customerRepo->findById($id);

        if (!$customer) {
            abort(404, 'Customer not found');
        }

        // Get customer addresses
        $addresses = DB::table('customer_addresses')
            ->where('customer_id', $id)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get customer orders with pagination
        $orders = DB::table('orders')
            ->where('customer_id', $id)
            ->select('orders.*')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Add items count to orders
        if (!$orders->isEmpty()) {
            $orderIds = $orders->pluck('id')->toArray();

            $orderStats = DB::table('order_items')
                ->whereIn('order_id', $orderIds)
                ->select('order_id', DB::raw('COUNT(*) as items_count'))
                ->groupBy('order_id')
                ->get()
                ->keyBy('order_id');

            foreach ($orders as $order) {
                $order->items_count = $orderStats[$order->id]->items_count ?? 0;
            }
        }

        return view('pages.customers.detail', compact('customer', 'addresses', 'orders'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:customers,email',
                'phone' => 'nullable|string|max:20',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|in:male,female,other',
                'password' => 'nullable|string|min:8',
            ]);

            DB::beginTransaction();

            // Generate customer code
            $lastCustomer = DB::table('customers')->orderBy('id', 'desc')->first();
            $nextId = $lastCustomer ? ($lastCustomer->id + 1) : 1;
            $validated['customer_code'] = 'CUST-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

            // Hash password if provided
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            // Set defaults
            $validated['segment'] = 'regular';
            $validated['is_vip'] = false;
            $validated['loyalty_points'] = 0;
            $validated['total_orders'] = 0;
            $validated['total_spent'] = 0;
            $validated['average_order_value'] = 0;
            $validated['status'] = 'active';

            $customer = $this->customerRepo->create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully',
                'data' => $customer
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:customers,email,' . $id,
                'phone' => 'nullable|string|max:20',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|in:male,female,other',
                'segment' => 'nullable|in:regular,premium,vip',
                'status' => 'nullable|in:active,inactive,blocked',
                'notes' => 'nullable|string',
            ]);

            DB::beginTransaction();

            // Update VIP status based on segment
            if (isset($validated['segment'])) {
                $validated['is_vip'] = $validated['segment'] === 'vip';
            }

            $customer = $this->customerRepo->update($id, $validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Customer updated successfully',
                'data' => $customer
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Check if customer has orders
            $ordersCount = DB::table('orders')
                ->where('customer_id', $id)
                ->count();

            if ($ordersCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete customer with existing orders'
                ], 400);
            }

            $this->customerRepo->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Customer deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            $customer = $this->customerRepo->findById($id);
            $newStatus = $customer->status === 'active' ? 'inactive' : 'active';

            $this->customerRepo->update($id, ['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => 'Customer status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
