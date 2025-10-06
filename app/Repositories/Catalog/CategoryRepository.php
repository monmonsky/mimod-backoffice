<?php

namespace App\Repositories\Catalog;

use App\Repositories\Contracts\Catalog\CategoryRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CategoryRepository implements CategoryRepositoryInterface
{
    protected $tableName = 'categories';

    private function table()
    {
        return DB::table($this->tableName);
    }

    public function getAll()
    {
        return $this->table()
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();
    }

    public function getAllActive()
    {
        return $this->table()
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();
    }

    public function getAllWithChildren()
    {
        $categories = $this->table()
            ->select(
                'categories.*',
                DB::raw('(SELECT COUNT(*) FROM products WHERE products.id IN (SELECT product_id FROM product_categories WHERE category_id = categories.id)) as product_count')
            )
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return $categories;
    }

    public function getTree()
    {
        // Get all categories
        $categories = $this->table()
            ->select(
                'categories.*',
                DB::raw('(SELECT COUNT(*) FROM products WHERE products.id IN (SELECT product_id FROM product_categories WHERE category_id = categories.id)) as product_count')
            )
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        // Build tree structure
        return $this->buildTree($categories);
    }

    private function buildTree($categories, $parentId = null)
    {
        $branch = [];

        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {
                $children = $this->buildTree($categories, $category->id);

                $categoryArray = (array) $category;
                if ($children) {
                    $categoryArray['children'] = $children;
                }

                $branch[] = (object) $categoryArray;
            }
        }

        return $branch;
    }

    public function getParents()
    {
        return $this->table()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
    }

    public function findById($id)
    {
        $category = $this->table()
            ->select(
                'categories.*',
                DB::raw('(SELECT COUNT(*) FROM products WHERE products.id IN (SELECT product_id FROM product_categories WHERE category_id = categories.id)) as product_count')
            )
            ->where('categories.id', $id)
            ->first();

        if (!$category) {
            throw new \Exception("Category not found");
        }

        return $category;
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
        // Check if has children
        if ($this->hasChildren($id)) {
            throw new \Exception("Cannot delete category with child categories");
        }

        // Check if has products
        if ($this->hasProducts($id)) {
            throw new \Exception("Cannot delete category with assigned products");
        }

        return $this->table()->where('id', $id)->delete();
    }

    public function toggleActive($id)
    {
        $category = $this->findById($id);

        $newStatus = !$category->is_active;

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
        $parents = $this->table()->whereNull('parent_id')->count();

        // Count total products across all categories
        $totalProducts = DB::table('product_categories')->distinct('product_id')->count('product_id');

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'parents' => $parents,
            'total_products' => $totalProducts
        ];
    }

    public function hasProducts($id)
    {
        $count = DB::table('product_categories')
            ->where('category_id', $id)
            ->count();

        return $count > 0;
    }

    public function hasChildren($id)
    {
        $count = $this->table()
            ->where('parent_id', $id)
            ->count();

        return $count > 0;
    }
}
