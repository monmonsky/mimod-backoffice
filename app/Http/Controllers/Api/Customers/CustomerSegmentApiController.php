<?php

namespace App\Http\Controllers\Api\Customers;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Contracts\Customers\CustomerSegmentRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerSegmentApiController extends Controller
{
    protected $segmentRepo;
    protected $responseBuilder;
    protected $response;

    public function __construct(CustomerSegmentRepositoryInterface $segmentRepository)
    {
        $this->segmentRepo = $segmentRepository;
        $this->responseBuilder = new ResultBuilder;
        $this->response = new Response;
    }

    public function index(Request $request)
    {
        try {
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

            $perPage = $request->get('per_page', 20);
            $segments = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();
            $statistics = $this->segmentRepo->getStatistics();

            $this->responseBuilder->setMessage("Customer segments retrieved successfully.");
            $this->responseBuilder->setData([
                'segments' => $segments,
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
            $segment = $this->segmentRepo->findById($id);

            if (!$segment) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Customer segment not found')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            $this->responseBuilder->setMessage("Customer segment retrieved successfully.");
            $this->responseBuilder->setData(['segment' => $segment]);
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

            if ($validator->fails()) {
                $this->responseBuilder->setStatus(false);
                $this->responseBuilder->setStatusCode('422');
                $this->responseBuilder->setMessage($validator->errors()->first());
                $this->responseBuilder->setData(['errors' => $validator->errors()]);
                return response()->json($this->response->generateResponse($this->responseBuilder), 422);
            }

            DB::beginTransaction();

            $validated = $validator->validated();
            $validated['customer_count'] = 0;
            $validated['is_active'] = $request->has('is_active');
            $validated['is_auto_assign'] = $request->has('is_auto_assign');

            $segment = $this->segmentRepo->create($validated);

            DB::commit();

            logActivity('create', "Created customer segment: {$segment->name}", 'customer_segment', $segment->id);

            $this->responseBuilder->setMessage("Customer segment created successfully.");
            $this->responseBuilder->setData(['segment' => $segment]);
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

            if ($validator->fails()) {
                $this->responseBuilder->setStatus(false);
                $this->responseBuilder->setStatusCode('422');
                $this->responseBuilder->setMessage($validator->errors()->first());
                $this->responseBuilder->setData(['errors' => $validator->errors()]);
                return response()->json($this->response->generateResponse($this->responseBuilder), 422);
            }

            DB::beginTransaction();

            $validated = $validator->validated();
            $validated['is_active'] = $request->has('is_active');
            $validated['is_auto_assign'] = $request->has('is_auto_assign');

            $segment = $this->segmentRepo->update($id, $validated);

            DB::commit();

            logActivity('update', "Updated customer segment: {$segment->name}", 'customer_segment', (int)$id);

            $this->responseBuilder->setMessage("Customer segment updated successfully.");
            $this->responseBuilder->setData(['segment' => $segment]);
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
            // Check if segment has customers
            $segment = $this->segmentRepo->findById($id);

            if ($segment && $segment->customer_count > 0) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage('Cannot delete segment with existing customers')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 400);
            }

            $segmentName = $segment ? $segment->name : "ID: {$id}";

            $this->segmentRepo->delete($id);

            logActivity('delete', "Deleted customer segment: {$segmentName}", 'customer_segment', (int)$id);

            $this->responseBuilder->setMessage("Customer segment deleted successfully.");
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
}
