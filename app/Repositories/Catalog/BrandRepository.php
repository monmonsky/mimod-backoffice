<?php

namespace App\Repositories\Catalog;

use App\Repositories\Contracts\Catalog\BrandRepositoryInterface;
use Illuminate\Support\Facades\DB;

class BrandRepository implements BrandRepositoryInterface
{
    protected $tableName = 'brands';

    private function table()
    {
        return DB::table($this->tableName);
    }

    public function getAll()
    {
        return $this->table()
            ->select(
                'brands.*',
                DB::raw('(SELECT COUNT(*) FROM products WHERE products.brand_id = brands.id) as product_count')
            )
            ->orderBy('name', 'asc')
            ->get();
    }

    public function getAllActive()
    {
        return $this->table()
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
    }

    public function findById($id)
    {
        $brand = $this->table()
            ->select(
                'brands.*',
                DB::raw('(SELECT COUNT(*) FROM products WHERE products.brand_id = brands.id) as product_count')
            )
            ->where('brands.id', $id)
            ->first();

        if (!$brand) {
            throw new \Exception("Brand not found");
        }

        return $brand;
    }

    public function create(array $data)
    {
        $data['created_at'] = now();
        $data['updated_at'] = now();

        $id = $this->table()->insertGetId($data);

        return $this->findById($id);
    }

    public function update($id, array $data)
    {
        $data['updated_at'] = now();

        $this->table()->where('id', $id)->update($data);

        return $this->findById($id);
    }

    public function delete($id)
    {
        // Check if has products
        if ($this->hasProducts($id)) {
            throw new \Exception("Cannot delete brand with assigned products");
        }

        return $this->table()->where('id', $id)->delete();
    }

    public function toggleActive($id)
    {
        $brand = $this->findById($id);

        $newStatus = !$brand->is_active;

        $this->table()->where('id', $id)->update([
            'is_active' => $newStatus,
            'updated_at' => now()
        ]);

        return $this->findById($id);
    }

    public function updateSortOrder($id, $sortOrder)
    {
        $this->table()->where('id', $id)->update([
            'sort_order' => $sortOrder,
            'updated_at' => now()
        ]);

        return $this->findById($id);
    }

    public function getStatistics()
    {
        $total = $this->table()->count();
        $active = $this->table()->where('is_active', true)->count();
        $inactive = $total - $active;

        // Count total products across all brands
        $totalProducts = DB::table('products')->whereNotNull('brand_id')->count();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'total_products' => $totalProducts
        ];
    }

    public function hasProducts($id)
    {
        $count = DB::table('products')
            ->where('brand_id', $id)
            ->count();

        return $count > 0;
    }
}
