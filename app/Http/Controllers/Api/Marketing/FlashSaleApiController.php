<?php

namespace App\Http\Controllers\Api\Marketing;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Contracts\Marketing\FlashSaleRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FlashSaleApiController extends Controller
{
    protected $flashSaleRepo;
    protected $responseBuilder;
    protected $response;

    public function __construct(FlashSaleRepositoryInterface $flashSaleRepository)
    {
        $this->flashSaleRepo = $flashSaleRepository;
        $this->responseBuilder = new ResultBuilder;
        $this->response = new Response;
    }

    public function index(Request $request)
    {
        try {
            $query = $this->flashSaleRepo->query();

            // Filter by name
            if ($request->filled('search')) {
                $query->where('name', 'ILIKE', "%{$request->search}%");
            }

            // Filter by status
            if ($request->filled('status')) {
                if ($request->status === 'active') {
                    $query->where('is_active', true)
                          ->where('start_time', '<=', now())
                          ->where('end_time', '>=', now());
                } elseif ($request->status === 'upcoming') {
                    $query->where('is_active', true)
                          ->where('start_time', '>', now());
                } elseif ($request->status === 'expired') {
                    $query->where('end_time', '<', now());
                }
            }

            // Sort by
            $sortBy = $request->get('sort_by', 'priority');
            $sortDirection = 'desc';

            if (in_array($sortBy, ['name'])) {
                $sortDirection = 'asc';
            }

            $perPage = $request->get('per_page', 20);
            $flashSales = $query->orderBy($sortBy, $sortDirection)
                ->paginate($perPage)
                ->withQueryString();

            $statistics = $this->flashSaleRepo->getStatistics();

            $this->responseBuilder->setMessage("Flash sales retrieved successfully.");
            $this->responseBuilder->setData([
                'flash_sales' => $flashSales,
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
            $flashSale = $this->flashSaleRepo->findById($id);

            if (!$flashSale) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Flash sale not found')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            $products = $this->flashSaleRepo->getProducts($id);

            $this->responseBuilder->setMessage("Flash sale retrieved successfully.");
            $this->responseBuilder->setData([
                'flash_sale' => $flashSale,
                'products' => $products
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
                'description' => 'nullable|string',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
                'is_active' => 'boolean',
                'priority' => 'integer|min:0',
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
            $flashSale = $this->flashSaleRepo->create($validated);

            DB::commit();

            logActivity('create', "Created flash sale: {$flashSale->name}", 'flash_sale', $flashSale->id);

            $this->responseBuilder->setMessage("Flash sale created successfully.");
            $this->responseBuilder->setData(['flash_sale' => $flashSale]);
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
                'description' => 'nullable|string',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
                'is_active' => 'boolean',
                'priority' => 'integer|min:0',
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
            $flashSale = $this->flashSaleRepo->update($id, $validated);

            DB::commit();

            logActivity('update', "Updated flash sale: {$flashSale->name}", 'flash_sale', (int)$id);

            $this->responseBuilder->setMessage("Flash sale updated successfully.");
            $this->responseBuilder->setData(['flash_sale' => $flashSale]);
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
            $flashSale = $this->flashSaleRepo->findById($id);
            $flashSaleName = $flashSale ? $flashSale->name : "ID: {$id}";

            $this->flashSaleRepo->delete($id);

            logActivity('delete', "Deleted flash sale: {$flashSaleName}", 'flash_sale', (int)$id);

            $this->responseBuilder->setMessage("Flash sale deleted successfully.");
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

    public function addProduct(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'discount_type' => 'required|in:percentage,fixed',
                'discount_value' => 'required|numeric|min:0',
                'stock_limit' => 'nullable|integer|min:1',
                'max_per_customer' => 'required|integer|min:1',
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
            $validated['sold_count'] = 0;
            $this->flashSaleRepo->addProduct($id, $validated['product_id'], $validated);

            DB::commit();

            logActivity('create', "Added product ID: {$validated['product_id']} to flash sale ID: {$id}", 'flash_sale', (int)$id);

            $this->responseBuilder->setMessage("Product added to flash sale successfully.");
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

    public function removeProduct(Request $request, $id)
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
            $this->flashSaleRepo->removeProduct($id, $validated['product_id']);

            logActivity('delete', "Removed product ID: {$validated['product_id']} from flash sale ID: {$id}", 'flash_sale', (int)$id);

            $this->responseBuilder->setMessage("Product removed from flash sale successfully.");
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
