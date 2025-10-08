<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Catalog\BrandRepository;
use Illuminate\Http\Request;

class BrandApiController extends Controller
{
    protected $brandRepo;
    protected $response;

    public function __construct(BrandRepository $brandRepo, Response $response)
    {
        $this->brandRepo = $brandRepo;
        $this->response = $response;
    }

    /**
     * Get all brands with pagination and filters
     */
    public function index(Request $request)
    {
        try {
            $query = $this->brandRepo->table();

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'ILIKE', '%' . $search . '%')
                      ->orWhere('slug', 'ILIKE', '%' . $search . '%')
                      ->orWhere('description', 'ILIKE', '%' . $search . '%');
                });
            }

            // Status filter
            if ($request->filled('status')) {
                $query->where('is_active', $request->status === 'active');
            }

            // Order and pagination
            $perPage = $request->input('per_page', 15);
            $brands = $query->orderBy('name', 'asc')
                           ->paginate($perPage);

            // Add product count for each brand
            foreach ($brands->items() as $brand) {
                $brand->product_count = \DB::table('products')
                    ->where('brand_id', $brand->id)
                    ->count();
            }

            // Get statistics
            $statistics = $this->getStatistics();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Brands retrieved successfully')
                ->setData([
                    'brands' => $brands,
                    'statistics' => $statistics
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve brands: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get brand statistics
     */
    private function getStatistics()
    {
        $total = $this->brandRepo->table()->count();
        $active = $this->brandRepo->table()->where('is_active', true)->count();
        $totalProducts = \DB::table('products')->whereNotNull('brand_id')->count();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $total - $active,
            'total_products' => $totalProducts
        ];
    }

    /**
     * Create new brand
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:brands,slug',
                'description' => 'nullable|string',
                'logo' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
                'is_active' => 'nullable|boolean'
            ]);

            \DB::beginTransaction();

            // Handle logo upload
            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $filename = time() . '_' . $logo->getClientOriginalName();
                $path = $logo->storeAs('brands', $filename, 'public');
                $validated['logo'] = $path;
            }

            $validated['is_active'] = $request->has('is_active') ? (bool)$request->is_active : true;

            $brand = $this->brandRepo->create($validated);

            \DB::commit();

            // Log activity
            logActivity('create', 'Created brand: ' . $brand->name, 'Brand', $brand->id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('201')
                ->setMessage('Brand created successfully')
                ->setData($brand);

            return response()->json($this->response->generateResponse($result), 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('422')
                ->setMessage('Validation failed')
                ->setData(['errors' => $e->errors()]);

            return response()->json($this->response->generateResponse($result), 422);
        } catch (\Exception $e) {
            \DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to create brand: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get single brand
     */
    public function show($id)
    {
        try {
            $brand = $this->brandRepo->findById($id);

            if (!$brand) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Brand not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Brand retrieved successfully')
                ->setData($brand);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve brand: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update brand
     */
    public function update(Request $request, $id)
    {
        try {
            $brand = $this->brandRepo->findById($id);

            if (!$brand) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Brand not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:brands,slug,' . $id,
                'description' => 'nullable|string',
                'logo' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
                'is_active' => 'nullable|boolean'
            ]);

            \DB::beginTransaction();

            // Handle logo upload
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($brand->logo && \Storage::disk('public')->exists($brand->logo)) {
                    \Storage::disk('public')->delete($brand->logo);
                }

                $logo = $request->file('logo');
                $filename = time() . '_' . $logo->getClientOriginalName();
                $path = $logo->storeAs('brands', $filename, 'public');
                $validated['logo'] = $path;
            }

            $validated['is_active'] = $request->has('is_active') ? (bool)$request->is_active : $brand->is_active;

            $brand = $this->brandRepo->update($id, $validated);

            \DB::commit();

            // Log activity
            logActivity('update', 'Updated brand: ' . $brand->name, 'Brand', $brand->id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Brand updated successfully')
                ->setData($brand);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('422')
                ->setMessage('Validation failed')
                ->setData(['errors' => $e->errors()]);

            return response()->json($this->response->generateResponse($result), 422);
        } catch (\Exception $e) {
            \DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update brand: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update brand status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'is_active' => 'required|boolean'
            ]);

            $brand = $this->brandRepo->update($id, [
                'is_active' => $request->is_active
            ]);

            // Log activity
            $status = $request->is_active ? 'activated' : 'deactivated';
            logActivity('update', 'Brand ' . $status . ': ' . $brand->name, 'Brand', $brand->id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Brand status updated successfully')
                ->setData($brand);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update brand status: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Delete brand
     */
    public function destroy($id)
    {
        try {
            $brand = $this->brandRepo->findById($id);

            if (!$brand) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Brand not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $brandName = $brand->name;

            // Check if has products
            if ($this->brandRepo->hasProducts($id)) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Cannot delete brand with assigned products. Please remove products first.')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            // Delete logo if exists
            if ($brand->logo && \Storage::disk('public')->exists($brand->logo)) {
                \Storage::disk('public')->delete($brand->logo);
            }

            \DB::beginTransaction();
            $this->brandRepo->delete($id);
            \DB::commit();

            // Log activity
            logActivity('delete', 'Deleted brand: ' . $brandName, 'Brand', $id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Brand deleted successfully')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to delete brand: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
