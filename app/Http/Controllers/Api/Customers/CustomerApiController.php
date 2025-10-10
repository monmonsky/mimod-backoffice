<?php

namespace App\Http\Controllers\Api\Customers;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomerApiController extends Controller
{
    protected $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Get all customers with filters
     */
    public function index(Request $request)
    {
        try {
            $query = DB::table('customers');

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%")
                        ->orWhere('email', 'ILIKE', "%{$search}%")
                        ->orWhere('phone', 'ILIKE', "%{$search}%")
                        ->orWhere('customer_code', 'ILIKE', "%{$search}%");
                });
            }

            // Status filter
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Segment filter
            if ($request->filled('segment')) {
                $query->where('segment', $request->segment);
            }

            // VIP filter
            if ($request->filled('is_vip')) {
                $query->where('is_vip', filter_var($request->is_vip, FILTER_VALIDATE_BOOLEAN));
            }

            // Gender filter
            if ($request->filled('gender')) {
                $query->where('gender', $request->gender);
            }

            $perPage = $request->get('per_page', 15);
            $customers = $query->orderBy('created_at', 'desc')->paginate($perPage);

            // Get statistics
            $statistics = [
                'total' => DB::table('customers')->count(),
                'active' => DB::table('customers')->where('status', 'active')->count(),
                'inactive' => DB::table('customers')->where('status', 'inactive')->count(),
                'vip' => DB::table('customers')->where('is_vip', true)->count(),
                'regular' => DB::table('customers')->where('segment', 'regular')->count(),
                'premium' => DB::table('customers')->where('segment', 'premium')->count(),
            ];

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Customers retrieved successfully')
                ->setData([
                    'customers' => $customers,
                    'statistics' => $statistics
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve customers: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get single customer by ID
     */
    public function show($id)
    {
        try {
            $customer = DB::table('customers')->where('id', $id)->first();

            if (!$customer) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Customer not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Get customer addresses
            $addresses = DB::table('customer_addresses')
                ->where('customer_id', $id)
                ->orderBy('is_default', 'desc')
                ->get();

            // Get recent orders
            $orders = DB::table('orders')
                ->where('customer_id', $id)
                ->select('id', 'order_number', 'status', 'total_amount', 'created_at')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // Decode preferences
            if ($customer->preferences) {
                $customer->preferences = json_decode($customer->preferences);
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Customer retrieved successfully')
                ->setData([
                    'customer' => $customer,
                    'addresses' => $addresses,
                    'recent_orders' => $orders,
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve customer: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Create new customer
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:customers,email',
                'phone' => 'required|string|max:20',
                'password' => 'required|string|min:6',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|in:male,female',
                'segment' => 'nullable|in:regular,premium,vip',
                'is_vip' => 'nullable|boolean',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Validation failed')
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            DB::beginTransaction();

            // Generate customer code
            $lastCustomer = DB::table('customers')->orderBy('id', 'desc')->first();
            $nextNumber = $lastCustomer ? ((int) substr($lastCustomer->customer_code, 5)) + 1 : 1;
            $customerCode = 'CUST-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            $customerId = DB::table('customers')->insertGetId([
                'customer_code' => $customerCode,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender ?? 'male',
                'segment' => $request->segment ?? 'regular',
                'is_vip' => $request->is_vip ?? false,
                'loyalty_points' => 0,
                'total_orders' => 0,
                'total_spent' => 0,
                'average_order_value' => 0,
                'status' => 'active',
                'notes' => $request->notes,
                'preferences' => json_encode([
                    'newsletter' => true,
                    'sms_notifications' => true,
                    'email_notifications' => true,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $customer = DB::table('customers')->where('id', $customerId)->first();

            DB::commit();

            logActivity('create', "Created customer: {$customer->name}", 'customer', (int)$customerId);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('201')
                ->setMessage('Customer created successfully')
                ->setData($customer);

            return response()->json($this->response->generateResponse($result), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to create customer: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update customer
     */
    public function update(Request $request, $id)
    {
        try {
            $customer = DB::table('customers')->where('id', $id)->first();

            if (!$customer) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Customer not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:customers,email,' . $id,
                'phone' => 'required|string|max:20',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|in:male,female',
                'segment' => 'nullable|in:regular,premium,vip',
                'is_vip' => 'nullable|boolean',
                'status' => 'nullable|in:active,inactive',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Validation failed')
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            DB::beginTransaction();

            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'segment' => $request->segment,
                'is_vip' => $request->is_vip ?? false,
                'status' => $request->status ?? $customer->status,
                'notes' => $request->notes,
                'updated_at' => now(),
            ];

            // Update password if provided
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            DB::table('customers')->where('id', $id)->update($updateData);

            $updatedCustomer = DB::table('customers')->where('id', $id)->first();

            DB::commit();

            logActivity('update', "Updated customer: {$updatedCustomer->name}", 'customer', (int)$id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Customer updated successfully')
                ->setData($updatedCustomer);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update customer: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update customer status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Validation failed')
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            $customer = DB::table('customers')->where('id', $id)->first();

            if (!$customer) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Customer not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            DB::table('customers')->where('id', $id)->update([
                'status' => $request->status,
                'updated_at' => now(),
            ]);

            $updatedCustomer = DB::table('customers')->where('id', $id)->first();

            logActivity('update', "Changed customer status to: {$request->status}", 'customer', (int)$id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Customer status updated successfully')
                ->setData($updatedCustomer);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update customer status: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Delete customer
     */
    public function destroy($id)
    {
        try {
            $customer = DB::table('customers')->where('id', $id)->first();

            if (!$customer) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Customer not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Check if customer has orders
            $hasOrders = DB::table('orders')->where('customer_id', $id)->exists();

            if ($hasOrders) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Cannot delete customer with existing orders')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            DB::beginTransaction();

            // Delete customer addresses
            DB::table('customer_addresses')->where('customer_id', $id)->delete();

            // Delete customer
            DB::table('customers')->where('id', $id)->delete();

            DB::commit();

            logActivity('delete', "Deleted customer: {$customer->name}", 'customer', (int)$id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Customer deleted successfully')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to delete customer: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get customer orders
     */
    public function orders($id, Request $request)
    {
        try {
            $customer = DB::table('customers')->where('id', $id)->first();

            if (!$customer) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Customer not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $query = DB::table('orders')->where('customer_id', $id);

            // Status filter
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $perPage = $request->get('per_page', 10);
            $orders = $query->orderBy('created_at', 'desc')->paginate($perPage);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Customer orders retrieved successfully')
                ->setData([
                    'customer' => $customer,
                    'orders' => $orders,
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve customer orders: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
