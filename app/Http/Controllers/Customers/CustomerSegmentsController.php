<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Customers\CustomerSegmentRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerSegmentsController extends Controller
{
    protected $segmentRepo;

    public function __construct(CustomerSegmentRepositoryInterface $segmentRepository)
    {
        $this->segmentRepo = $segmentRepository;
    }

    public function index(Request $request)
    {
        $query = $this->segmentRepo->query();

        // Name filter
        if ($request->filled('name')) {
            $query->where('name', 'ILIKE', "%{$request->name}%");
        }

        // Code filter
        if ($request->filled('code')) {
            $query->where('code', 'ILIKE', "%{$request->code}%");
        }

        // Active filter
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === '1');
        }

        // Auto assign filter
        if ($request->filled('is_auto_assign')) {
            $query->where('is_auto_assign', $request->is_auto_assign === '1');
        }

        $segments = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $statistics = $this->segmentRepo->getStatistics();

        return view('pages.customers.customer-segments.index', compact('segments', 'statistics'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:customer_segments,code',
                'description' => 'nullable|string',
                'color' => 'nullable|string|max:20',
                'min_orders' => 'nullable|integer|min:0',
                'max_orders' => 'nullable|integer|min:0',
                'min_spent' => 'nullable|numeric|min:0',
                'max_spent' => 'nullable|numeric|min:0',
                'min_loyalty_points' => 'nullable|integer|min:0',
                'days_since_last_order' => 'nullable|integer|min:0',
                'is_active' => 'boolean',
                'is_auto_assign' => 'boolean',
            ]);

            DB::beginTransaction();

            // Set defaults
            $validated['customer_count'] = 0;
            $validated['is_active'] = $request->has('is_active');
            $validated['is_auto_assign'] = $request->has('is_auto_assign');

            $segment = $this->segmentRepo->create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Segment created successfully',
                'data' => $segment
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $segment = $this->segmentRepo->findById($id);

            return response()->json([
                'success' => true,
                'data' => $segment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:customer_segments,code,' . $id,
                'description' => 'nullable|string',
                'color' => 'nullable|string|max:20',
                'min_orders' => 'nullable|integer|min:0',
                'max_orders' => 'nullable|integer|min:0',
                'min_spent' => 'nullable|numeric|min:0',
                'max_spent' => 'nullable|numeric|min:0',
                'min_loyalty_points' => 'nullable|integer|min:0',
                'days_since_last_order' => 'nullable|integer|min:0',
                'is_active' => 'boolean',
                'is_auto_assign' => 'boolean',
            ]);

            DB::beginTransaction();

            $validated['is_active'] = $request->has('is_active');
            $validated['is_auto_assign'] = $request->has('is_auto_assign');

            $segment = $this->segmentRepo->update($id, $validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Segment updated successfully',
                'data' => $segment
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
            // Check if segment has customers
            $segment = $this->segmentRepo->findById($id);

            if ($segment->customer_count > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete segment with existing customers'
                ], 400);
            }

            $this->segmentRepo->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Segment deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function recalculateCustomers($id)
    {
        try {
            $segment = $this->segmentRepo->findById($id);

            // Build query to count customers matching criteria
            $query = DB::table('customers')->where('status', 'active');

            if ($segment->min_orders) {
                $query->where('total_orders', '>=', $segment->min_orders);
            }

            if ($segment->max_orders) {
                $query->where('total_orders', '<=', $segment->max_orders);
            }

            if ($segment->min_spent) {
                $query->where('total_spent', '>=', $segment->min_spent);
            }

            if ($segment->max_spent) {
                $query->where('total_spent', '<=', $segment->max_spent);
            }

            if ($segment->min_loyalty_points) {
                $query->where('loyalty_points', '>=', $segment->min_loyalty_points);
            }

            $count = $query->count();

            // Update segment customer count
            $this->segmentRepo->update($id, ['customer_count' => $count]);

            return response()->json([
                'success' => true,
                'message' => 'Customer count recalculated successfully',
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
