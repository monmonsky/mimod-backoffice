<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Catalog\BrandRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BrandsController extends Controller
{
    protected $brandRepo;

    public function __construct(BrandRepositoryInterface $brandRepository)
    {
        $this->brandRepo = $brandRepository;
    }

    /**
     * Display brands page
     */
    public function brands()
    {
        $brands = $this->brandRepo->getAll();
        $statistics = $this->brandRepo->getStatistics();

        return view('pages.catalog.brands.brands', compact('brands', 'statistics'));
    }

    /**
     * Store new brand
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'nullable|string|unique:brands,slug|max:255',
                'logo' => 'nullable|image|max:2048',
                'description' => 'nullable|string',
                'is_active' => 'nullable|boolean',
            ]);

            // Auto-generate slug if not provided
            if (empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            // Handle logo upload
            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $logoName = time() . '_' . Str::slug($validated['name']) . '.' . $logo->extension();
                $logoPath = $logo->storeAs('brands', $logoName, 'public');
                $validated['logo'] = $logoPath;
            }

            // Set defaults
            $validated['is_active'] = $validated['is_active'] ?? true;

            DB::beginTransaction();

            $brand = $this->brandRepo->create($validated);

            DB::commit();

            // Log activity
            logActivity('create', 'Created new brand: ' . $brand->name, 'Brand', $brand->id);

            return response()->json([
                'success' => true,
                'message' => 'Brand created successfully',
                'data' => $brand
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create brand: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update brand
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:brands,slug,' . $id,
                'logo' => 'nullable|image|max:2048',
                'description' => 'nullable|string',
                'is_active' => 'nullable|boolean',
            ]);

            // Handle logo upload
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                $oldBrand = $this->brandRepo->findById($id);
                if ($oldBrand->logo && \Storage::disk('public')->exists($oldBrand->logo)) {
                    \Storage::disk('public')->delete($oldBrand->logo);
                }

                $logo = $request->file('logo');
                $logoName = time() . '_' . Str::slug($validated['name']) . '.' . $logo->extension();
                $logoPath = $logo->storeAs('brands', $logoName, 'public');
                $validated['logo'] = $logoPath;
            }

            DB::beginTransaction();

            $brand = $this->brandRepo->update($id, $validated);

            DB::commit();

            // Log activity
            logActivity('update', 'Updated brand: ' . $brand->name, 'Brand', $brand->id);

            return response()->json([
                'success' => true,
                'message' => 'Brand updated successfully',
                'data' => $brand
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update brand: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete brand
     */
    public function destroy($id)
    {
        try {
            $brand = $this->brandRepo->findById($id);
            $brandName = $brand->name;

            // Check if has products
            if ($this->brandRepo->hasProducts($id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete brand with assigned products. Please remove products first.'
                ], 422);
            }

            // Delete logo if exists
            if ($brand->logo && \Storage::disk('public')->exists($brand->logo)) {
                \Storage::disk('public')->delete($brand->logo);
            }

            DB::beginTransaction();

            $this->brandRepo->delete($id);

            DB::commit();

            // Log activity
            logActivity('delete', 'Deleted brand: ' . $brandName, 'Brand', $id);

            return response()->json([
                'success' => true,
                'message' => 'Brand deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete brand: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        try {
            $brand = $this->brandRepo->toggleActive($id);

            // Log activity
            $status = $brand->is_active ? 'activated' : 'deactivated';
            logActivity('update', 'Brand ' . $status . ': ' . $brand->name, 'Brand', $brand->id);

            return response()->json([
                'success' => true,
                'message' => 'Brand status updated successfully',
                'data' => $brand
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete brand logo
     */
    public function deleteLogo($id)
    {
        try {
            $brand = $this->brandRepo->findById($id);

            if (!$brand->logo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Brand has no logo'
                ], 422);
            }

            // Delete logo file
            if (\Storage::disk('public')->exists($brand->logo)) {
                \Storage::disk('public')->delete($brand->logo);
            }

            // Update database
            $this->brandRepo->update($id, ['logo' => null]);

            // Log activity
            logActivity('update', 'Deleted logo for brand: ' . $brand->name, 'Brand', $brand->id);

            return response()->json([
                'success' => true,
                'message' => 'Brand logo deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete logo: ' . $e->getMessage()
            ], 500);
        }
    }
}
