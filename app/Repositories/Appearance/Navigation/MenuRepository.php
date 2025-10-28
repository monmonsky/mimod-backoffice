<?php

namespace App\Repositories\Appearance\Navigation;

use App\Events\MenuUpdated;
use App\Repositories\Appearance\Navigation\Contracts\MenuRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class MenuRepository implements MenuRepositoryInterface
{
    /**
     * Get all menus with optional filters
     */
    public function getAllMenus(array $filters = [])
    {
        $query = DB::table('menus');

        // Filter by parent_id
        if (isset($filters['parent_id'])) {
            if ($filters['parent_id'] === 'null') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $filters['parent_id']);
            }
        }

        // Filter by active status
        if (isset($filters['active'])) {
            $query->where('is_active', $filters['active']);
        }

        // Filter by location
        if (isset($filters['location'])) {
            $query->whereJsonContains('menu_locations', $filters['location']);
        }

        // Filter by link_type
        if (isset($filters['link_type'])) {
            $query->where('link_type', $filters['link_type']);
        }

        // Search by title
        if (isset($filters['search'])) {
            $query->where('title', 'ILIKE', '%' . $filters['search'] . '%');
        }

        // Order
        $query->orderBy('order', 'asc')->orderBy('id', 'asc');

        // Pagination
        if (isset($filters['per_page'])) {
            return $query->paginate($filters['per_page']);
        }

        return $query->get();
    }

    /**
     * Get menu by ID
     */
    public function findById(int $id)
    {
        $menu = DB::table('menus')->where('id', $id)->first();

        if (!$menu) {
            return null;
        }

        // Decode JSON fields
        if (isset($menu->menu_locations) && is_string($menu->menu_locations)) {
            $menu->menu_locations = json_decode($menu->menu_locations, true);
        }

        if (isset($menu->meta) && is_string($menu->meta)) {
            $menu->meta = json_decode($menu->meta, true);
        }

        return $menu;
    }

    /**
     * Get menu tree (nested structure) by location
     */
    public function getMenuTree(string $location = 'header', bool $activeOnly = true)
    {
        $cacheKey = "menu_tree_{$location}_" . ($activeOnly ? 'active' : 'all');

        return Cache::remember($cacheKey, 3600, function () use ($location, $activeOnly) {
            $query = DB::table('menus')
                ->whereJsonContains('menu_locations', $location);

            if ($activeOnly) {
                $query->where('is_active', true);
            }

            $menus = $query->orderBy('order', 'asc')->get()->toArray();

            // Convert to array and decode JSON
            $menusArray = array_map(function ($menu) {
                $menuArray = (array) $menu;

                if (isset($menuArray['menu_locations']) && is_string($menuArray['menu_locations'])) {
                    $menuArray['menu_locations'] = json_decode($menuArray['menu_locations'], true);
                }

                if (isset($menuArray['meta']) && is_string($menuArray['meta'])) {
                    $menuArray['meta'] = json_decode($menuArray['meta'], true);
                }

                return $menuArray;
            }, $menus);

            return $this->buildTree($menusArray);
        });
    }

    /**
     * Get parent menus only
     */
    public function getParentMenus(array $filters = [])
    {
        $filters['parent_id'] = 'null';
        return $this->getAllMenus($filters);
    }

    /**
     * Create new menu
     */
    public function create(array $data)
    {
        // Generate slug if not provided
        if (!isset($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Auto-generate URL based on link_type
        if (!isset($data['url']) || empty($data['url'])) {
            $data['url'] = $this->generateUrl($data);
        }

        // Encode JSON fields
        if (isset($data['menu_locations']) && is_array($data['menu_locations'])) {
            $data['menu_locations'] = json_encode($data['menu_locations']);
        }

        if (isset($data['meta']) && is_array($data['meta'])) {
            $data['meta'] = json_encode($data['meta']);
        }

        $data['created_at'] = now();
        $data['updated_at'] = now();

        $id = DB::table('menus')->insertGetId($data);

        // Dispatch event to clear cache
        event(new MenuUpdated('created', $id));

        return $this->findById($id);
    }

    /**
     * Update menu
     */
    public function update(int $id, array $data)
    {
        // Auto-generate URL if link_type or related IDs changed
        if (isset($data['link_type']) || isset($data['category_id']) || isset($data['brand_id'])) {
            $existingMenu = $this->findById($id);
            $mergedData = array_merge((array) $existingMenu, $data);
            $data['url'] = $this->generateUrl($mergedData);
        }

        // Encode JSON fields
        if (isset($data['menu_locations']) && is_array($data['menu_locations'])) {
            $data['menu_locations'] = json_encode($data['menu_locations']);
        }

        if (isset($data['meta']) && is_array($data['meta'])) {
            $data['meta'] = json_encode($data['meta']);
        }

        $data['updated_at'] = now();

        DB::table('menus')->where('id', $id)->update($data);

        // Dispatch event to clear cache
        event(new MenuUpdated('updated', $id));

        return $this->findById($id);
    }

    /**
     * Delete menu
     */
    public function delete(int $id)
    {
        // Check if has children
        $hasChildren = DB::table('menus')->where('parent_id', $id)->exists();

        if ($hasChildren) {
            throw new \Exception('Cannot delete menu with children. Please delete children first or use cascade delete.');
        }

        $deleted = DB::table('menus')->where('id', $id)->delete();

        // Dispatch event to clear cache
        event(new MenuUpdated('deleted', $id));

        return $deleted;
    }

    /**
     * Reorder menus
     */
    public function reorder(array $orders)
    {
        foreach ($orders as $order) {
            DB::table('menus')
                ->where('id', $order['id'])
                ->update([
                    'order' => $order['order'],
                    'updated_at' => now()
                ]);
        }

        // Dispatch event to clear cache
        event(new MenuUpdated('reordered'));

        return true;
    }

    /**
     * Bulk create menus from categories
     */
    public function bulkCreateFromCategories(array $data)
    {
        $categoryIds = $data['categories'];
        $parentId = $data['parent_id'] ?? null;

        $categories = DB::table('categories')
            ->whereIn('id', $categoryIds)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        $created = [];
        $order = DB::table('menus')->where('parent_id', $parentId)->max('order') ?? 0;

        foreach ($categories as $category) {
            $order++;

            $menuData = [
                'title' => $data['auto_title'] ? $category->name : ($data['title_prefix'] ?? '') . $category->name,
                'slug' => ($data['slug_prefix'] ?? '') . $category->slug,
                'url' => '/collections/' . $category->slug,
                'link_type' => 'category',
                'category_id' => $category->id,
                'brand_id' => null,
                'parent_id' => $parentId,
                'icon' => $data['icon'] ?? null,
                'description' => 'Category: ' . $category->name,
                'order' => $order,
                'is_clickable' => $data['is_clickable'] ?? true,
                'is_active' => $data['is_active'] ?? true,
                'target' => $data['target'] ?? '_self',
                'menu_locations' => json_encode($data['menu_locations'] ?? ['header']),
                'meta' => json_encode(['category_id' => $category->id]),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $id = DB::table('menus')->insertGetId($menuData);
            $created[] = $this->findById($id);
        }

        // Dispatch event to clear cache
        event(new MenuUpdated('bulk_created_categories'));

        return $created;
    }

    /**
     * Bulk create menus from brands
     */
    public function bulkCreateFromBrands(array $data)
    {
        $brandIds = $data['brands'];
        $parentId = $data['parent_id'] ?? null;

        $brands = DB::table('brands')
            ->whereIn('id', $brandIds)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        $created = [];
        $order = DB::table('menus')->where('parent_id', $parentId)->max('order') ?? 0;

        foreach ($brands as $brand) {
            $order++;

            $menuData = [
                'title' => $data['auto_title'] ? $brand->name : ($data['title_prefix'] ?? '') . $brand->name,
                'slug' => ($data['slug_prefix'] ?? '') . $brand->slug,
                'url' => '/brand/' . $brand->slug,
                'link_type' => 'brand',
                'category_id' => null,
                'brand_id' => $brand->id,
                'parent_id' => $parentId,
                'icon' => $data['icon'] ?? null,
                'description' => 'Brand: ' . $brand->name,
                'order' => $order,
                'is_clickable' => $data['is_clickable'] ?? true,
                'is_active' => $data['is_active'] ?? true,
                'target' => $data['target'] ?? '_self',
                'menu_locations' => json_encode($data['menu_locations'] ?? ['header']),
                'meta' => json_encode(['brand_id' => $brand->id]),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $id = DB::table('menus')->insertGetId($menuData);
            $created[] = $this->findById($id);
        }

        // Dispatch event to clear cache
        event(new MenuUpdated('bulk_created_brands'));

        return $created;
    }

    /**
     * Get menus for dropdown/select (admin)
     */
    public function getMenusForSelect()
    {
        return DB::table('menus')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('order', 'asc')
            ->get(['id', 'title', 'slug']);
    }

    /**
     * Build menu tree from flat array
     */
    public function buildTree(array $elements, int $parentId = null)
    {
        $branch = [];

        foreach ($elements as $element) {
            $elementParentId = $element['parent_id'] ?? null;

            if ($elementParentId == $parentId) {
                $children = $this->buildTree($elements, $element['id']);

                if ($children) {
                    $element['children'] = $children;
                } else {
                    $element['children'] = [];
                }

                $element['has_children'] = !empty($element['children']);

                $branch[] = $element;
            }
        }

        return $branch;
    }

    /**
     * Generate URL based on link_type
     */
    private function generateUrl(array $data)
    {
        switch ($data['link_type']) {
            case 'category':
                if (isset($data['category_id'])) {
                    $category = DB::table('categories')->where('id', $data['category_id'])->first();
                    return $category ? '/collections/' . $category->slug : '#';
                }
                return '#';

            case 'brand':
                if (isset($data['brand_id'])) {
                    $brand = DB::table('brands')->where('id', $data['brand_id'])->first();
                    return $brand ? '/brand/' . $brand->slug : '#';
                }
                return '#';

            case 'static':
            case 'custom':
                return $data['url'] ?? '#';

            case 'none':
            default:
                return '#';
        }
    }

    /**
     * Clear menu cache
     */
    // clearMenuCache() method removed - now handled by MenuUpdated event & ClearMenuCache listener
}
