<?php

namespace App\Http\Controllers\Api\Marketing;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Contracts\Marketing\CouponRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CouponApiController extends Controller
{
    protected $couponRepo;
    protected $responseBuilder;
    protected $response;

    public function __construct(CouponRepositoryInterface $couponRepository)
    {
        $this->couponRepo = $couponRepository;
        $this->responseBuilder = new ResultBuilder;
        $this->response = new Response;
    }

    public function index(Request $request)
    {
        try {
            $query = $this->couponRepo->query();

            // Filter by code or name
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('code', 'ILIKE', "%{$search}%")
                      ->orWhere('name', 'ILIKE', "%{$search}%");
                });
            }

            // Filter by type
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            // Filter by status
            if ($request->filled('status')) {
                if ($request->status === 'active') {
                    $query->where('is_active', true)
                          ->where('start_date', '<=', now())
                          ->where('end_date', '>=', now());
                } elseif ($request->status === 'expired') {
                    $query->where('end_date', '<', now());
                } elseif ($request->status === 'upcoming') {
                    $query->where('start_date', '>', now());
                } elseif ($request->status === 'inactive') {
                    $query->where('is_active', false);
                }
            }

            // Sort by
            $sortBy = $request->get('sort_by', 'created_at');
            $sortDirection = 'desc';

            if (in_array($sortBy, ['code', 'name'])) {
                $sortDirection = 'asc';
            }

            $perPage = $request->get('per_page', 20);
            $coupons = $query->orderBy($sortBy, $sortDirection)
                ->paginate($perPage)
                ->withQueryString();

            $statistics = $this->couponRepo->getStatistics();

            $this->responseBuilder->setMessage("Coupons retrieved successfully.");
            $this->responseBuilder->setData([
                'coupons' => $coupons,
                'statistics' => $statistics
            ]);
            return $this->response->generateResponse($this->responseBuilder);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage($e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    public function show($id)
    {
        try {
            $coupon = $this->couponRepo->findById($id);

            if (!$coupon) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Coupon not found')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            $usage = $this->couponRepo->getUsageHistory($id);

            $this->responseBuilder->setMessage("Coupon retrieved successfully.");
            $this->responseBuilder->setData([
                'coupon' => $coupon,
                'usage' => $usage
            ]);
            return $this->response->generateResponse($this->responseBuilder);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage($e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|max:50|unique:coupons,code',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'type' => 'required|in:percentage,fixed,free_shipping',
                'value' => 'required|numeric|min:0',
                'min_purchase' => 'nullable|numeric|min:0',
                'max_discount' => 'nullable|numeric|min:0',
                'usage_limit' => 'nullable|integer|min:1',
                'usage_limit_per_customer' => 'required|integer|min:1',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'is_active' => 'boolean',
                'applicable_products' => 'nullable|array',
                'applicable_categories' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                $this->responseBuilder->setStatus(false);
                $this->responseBuilder->setStatusCode('422');
                $this->responseBuilder->setMessage($validator->errors()->first());
                $this->responseBuilder->setData(['errors' => $validator->errors()]);
                return response()->json($this->response->generateResponse($this->responseBuilder), 422);
            }

            DB::beginTransaction();

            $validated = $validator->validated();
            $validated['created_by'] = auth()->id();
            $validated['usage_count'] = 0;

            if (isset($validated['applicable_products'])) {
                $validated['applicable_products'] = json_encode($validated['applicable_products']);
            }

            if (isset($validated['applicable_categories'])) {
                $validated['applicable_categories'] = json_encode($validated['applicable_categories']);
            }

            $coupon = $this->couponRepo->create($validated);

            DB::commit();

            logActivity('create', "Created coupon: {$coupon->code}", 'coupon', $coupon->id);

            $this->responseBuilder->setMessage("Coupon created successfully.");
            $this->responseBuilder->setData(['coupon' => $coupon]);
            return response()->json($this->response->generateResponse($this->responseBuilder), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage($e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|max:50|unique:coupons,code,' . $id,
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'type' => 'required|in:percentage,fixed,free_shipping',
                'value' => 'required|numeric|min:0',
                'min_purchase' => 'nullable|numeric|min:0',
                'max_discount' => 'nullable|numeric|min:0',
                'usage_limit' => 'nullable|integer|min:1',
                'usage_limit_per_customer' => 'required|integer|min:1',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'is_active' => 'boolean',
                'applicable_products' => 'nullable|array',
                'applicable_categories' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                $this->responseBuilder->setStatus(false);
                $this->responseBuilder->setStatusCode('422');
                $this->responseBuilder->setMessage($validator->errors()->first());
                $this->responseBuilder->setData(['errors' => $validator->errors()]);
                return response()->json($this->response->generateResponse($this->responseBuilder), 422);
            }

            DB::beginTransaction();

            $validated = $validator->validated();

            if (isset($validated['applicable_products'])) {
                $validated['applicable_products'] = json_encode($validated['applicable_products']);
            }

            if (isset($validated['applicable_categories'])) {
                $validated['applicable_categories'] = json_encode($validated['applicable_categories']);
            }

            $coupon = $this->couponRepo->update($id, $validated);

            DB::commit();

            logActivity('update', "Updated coupon: {$coupon->code}", 'coupon', (int)$id);

            $this->responseBuilder->setMessage("Coupon updated successfully.");
            $this->responseBuilder->setData(['coupon' => $coupon]);
            return $this->response->generateResponse($this->responseBuilder);
        } catch (\Exception $e) {
            DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage($e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $coupon = $this->couponRepo->findById($id);
            $couponCode = $coupon ? $coupon->code : "ID: {$id}";

            $this->couponRepo->delete($id);

            logActivity('delete', "Deleted coupon: {$couponCode}", 'coupon', (int)$id);

            $this->responseBuilder->setMessage("Coupon deleted successfully.");
            $this->responseBuilder->setData([]);
            return $this->response->generateResponse($this->responseBuilder);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage($e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    public function validate(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string',
                'customer_id' => 'required|integer',
                'cart_amount' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                $this->responseBuilder->setStatus(false);
                $this->responseBuilder->setStatusCode('422');
                $this->responseBuilder->setMessage($validator->errors()->first());
                $this->responseBuilder->setData(['errors' => $validator->errors()]);
                return response()->json($this->response->generateResponse($this->responseBuilder), 422);
            }

            $validated = $validator->validated();

            $result = $this->couponRepo->validateCoupon(
                $validated['code'],
                $validated['customer_id'],
                $validated['cart_amount']
            );

            $this->responseBuilder->setMessage("Coupon validation completed.");
            $this->responseBuilder->setData($result);
            return $this->response->generateResponse($this->responseBuilder);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage($e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
