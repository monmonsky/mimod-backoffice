<?php

namespace App\Http\Controllers\Api\Marketing;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Contracts\Marketing\BundleDealRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BundleDealApiController extends Controller
{
    protected $bundleRepo;
    protected $responseBuilder;
    protected $response;

    public function __construct(BundleDealRepositoryInterface $bundleRepository)
    {
        $this->bundleRepo = $bundleRepository;
        $this->responseBuilder = new ResultBuilder;
        $this->response = new Response;
    }

    public function index(Request $request)
    {
        try {
            $query = $this->bundleRepo->query();

            // Filter by name
            if ($request->filled('search')) {
                $query->where('name', 'ILIKE', "%{$request->search}%");
            }

            // Filter by status
            if ($request->filled('status')) {
                if ($request->status === 'active') {
                    $query->where('is_active', true)
                          ->where('start_date', '<=', now())
                          ->where('end_date', '>=', now());
                } elseif ($request->status === 'upcoming') {
                    $query->where('is_active', true)
                          ->where('start_date', '>', now());
                } elseif ($request->status === 'expired') {
                    $query->where('end_date', '<', now());
                }
            }

            // Sort by
            $sortBy = $request->get('sort_by', 'created_at');
            $sortDirection = 'desc';

            if (in_array($sortBy, ['name'])) {
                $sortDirection = 'asc';
            }

            $perPage = $request->get('per_page', 20);
            $bundles = $query->orderBy($sortBy, $sortDirection)
                ->paginate($perPage)
                ->withQueryString();

            $statistics = $this->bundleRepo->getStatistics();

            $this->responseBuilder->setMessage("Bundle deals retrieved successfully.");
            $this->responseBuilder->setData([
                'bundle_deals' => $bundles,
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
            $bundle = $this->bundleRepo->findById($id);

            if (!$bundle) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Bundle deal not found')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            $items = $this->bundleRepo->getItems($id);

            $this->responseBuilder->setMessage("Bundle deal retrieved successfully.");
            $this->responseBuilder->setData([
                'bundle' => $bundle,
                'items' => $items
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
                'name' => 'required|string|max:255',
                'slug' => 'nullable|string|unique:bundle_deals,slug',
                'description' => 'nullable|string',
                'bundle_price' => 'required|numeric|min:0',
                'original_price' => 'required|numeric|min:0',
                'stock_limit' => 'nullable|integer|min:1',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'is_active' => 'boolean',
                'image' => 'nullable|string',
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

            // Generate slug if not provided
            if (!isset($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            $validated['created_by'] = auth()->id();
            $validated['sold_count'] = 0;

            $bundle = $this->bundleRepo->create($validated);

            DB::commit();

            logActivity('create', "Created bundle deal: {$bundle->name}", 'bundle_deal', $bundle->id);

            $this->responseBuilder->setMessage("Bundle deal created successfully.");
            $this->responseBuilder->setData(['bundle' => $bundle]);
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
                'slug' => 'nullable|string|unique:bundle_deals,slug,' . $id,
                'description' => 'nullable|string',
                'bundle_price' => 'required|numeric|min:0',
                'original_price' => 'required|numeric|min:0',
                'stock_limit' => 'nullable|integer|min:1',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'is_active' => 'boolean',
                'image' => 'nullable|string',
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
            $bundle = $this->bundleRepo->update($id, $validated);

            DB::commit();

            logActivity('update', "Updated bundle deal: {$bundle->name}", 'bundle_deal', (int)$id);

            $this->responseBuilder->setMessage("Bundle deal updated successfully.");
            $this->responseBuilder->setData(['bundle' => $bundle]);
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
            $bundle = $this->bundleRepo->findById($id);
            $bundleName = $bundle ? $bundle->name : "ID: {$id}";

            $this->bundleRepo->delete($id);

            logActivity('delete', "Deleted bundle deal: {$bundleName}", 'bundle_deal', (int)$id);

            $this->responseBuilder->setMessage("Bundle deal deleted successfully.");
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

    public function addItem(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0',
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
            $this->bundleRepo->addItem(
                $id,
                $validated['product_id'],
                $validated['quantity'],
                $validated['price']
            );

            DB::commit();

            logActivity('create', "Added product ID: {$validated['product_id']} to bundle deal ID: {$id}", 'bundle_deal', (int)$id);

            $this->responseBuilder->setMessage("Product added to bundle successfully.");
            $this->responseBuilder->setData([]);
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

    public function removeItem(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
            ]);

            if ($validator->fails()) {
                $this->responseBuilder->setStatus(false);
                $this->responseBuilder->setStatusCode('422');
                $this->responseBuilder->setMessage($validator->errors()->first());
                $this->responseBuilder->setData(['errors' => $validator->errors()]);
                return response()->json($this->response->generateResponse($this->responseBuilder), 422);
            }

            $validated = $validator->validated();
            $this->bundleRepo->removeItem($id, $validated['product_id']);

            logActivity('delete', "Removed product ID: {$validated['product_id']} from bundle deal ID: {$id}", 'bundle_deal', (int)$id);

            $this->responseBuilder->setMessage("Product removed from bundle successfully.");
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
