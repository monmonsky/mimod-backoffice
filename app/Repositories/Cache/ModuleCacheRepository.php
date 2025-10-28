<?php

namespace App\Repositories\Cache;

use App\Repositories\CacheRepository;
use App\Repositories\Contracts\AccessControl\ModuleRepositoryInterface;

class ModuleCacheRepository extends CacheRepository
{
    protected string $cachePrefix = 'modules';
    protected int $cacheTTL = 86400; // 24 hours

    private ModuleRepositoryInterface $moduleRepo;

    public function __construct(ModuleRepositoryInterface $moduleRepo)
    {
        $this->moduleRepo = $moduleRepo;
    }

    /**
     * Get all modules (cached)
     */
    public function getAll()
    {
        return $this->remember('all', function () {
            return $this->moduleRepo->getAll();
        });
    }

    /**
     * Get all modules with children (cached)
     */
    public function getAllWithChildren()
    {
        return $this->remember('all_with_children', function () {
            return $this->moduleRepo->getAllWithChildren();
        });
    }

    /**
     * Get parent modules only (cached)
     */
    public function getParents()
    {
        return $this->remember('parents', function () {
            return $this->moduleRepo->getParents();
        });
    }

    /**
     * Get active modules (cached)
     */
    public function getActive()
    {
        return $this->remember('active', function () {
            return $this->moduleRepo->getActive();
        });
    }

    /**
     * Get visible modules (cached)
     */
    public function getVisible()
    {
        return $this->remember('visible', function () {
            return $this->moduleRepo->getVisible();
        });
    }

    /**
     * Get visible modules with children for sidebar (cached)
     */
    public function getVisibleWithChildren()
    {
        return $this->remember('visible_with_children', function () {
            $parents = $this->moduleRepo->getParents();
            $visibleParents = [];

            foreach ($parents as $parent) {
                // Only include visible and active parents
                if (!$parent->is_visible || !$parent->is_active) {
                    continue;
                }

                // Get children
                $children = collect($this->moduleRepo->getAll())
                    ->where('parent_id', $parent->id)
                    ->where('is_visible', true)
                    ->where('is_active', true)
                    ->values()
                    ->all();

                $parent->children = $children;
                $visibleParents[] = $parent;
            }

            return $visibleParents;
        });
    }

    /**
     * Get module by ID (cached)
     */
    public function findById($id)
    {
        return $this->remember("module:{$id}", function () use ($id) {
            return $this->moduleRepo->findById($id);
        });
    }

    /**
     * Get modules grouped by group_name (cached)
     * Returns only one representative module per group (for index page)
     */
    public function getGroupedModules()
    {
        return $this->remember('grouped_modules', function () {
            $allModules = $this->moduleRepo->getAll();

            // Group by group_name and get first module of each group (as representative)
            $groupedModules = $allModules->whereNull('parent_id')
                ->groupBy('group_name')
                ->map(function ($group) {
                    return $group->sortBy('sort_order')->first();
                })
                ->sortBy('sort_order')
                ->values();

            return $groupedModules;
        });
    }

    /**
     * Get all modules in a specific group with their children (cached)
     */
    public function getModulesByGroup($groupName)
    {
        return $this->remember("group:{$groupName}", function () use ($groupName) {
            $modules = $this->moduleRepo->getAll()
                ->where('group_name', $groupName)
                ->sortBy('sort_order');

            // Attach children to parent modules
            $parentModules = $modules->whereNull('parent_id');

            foreach ($parentModules as $parent) {
                $parent->children = $modules
                    ->where('parent_id', $parent->id)
                    ->values()
                    ->all();
            }

            return $parentModules->values();
        });
    }

    /**
     * Get module statistics (cached)
     */
    public function getStatistics()
    {
        return $this->remember('statistics', function () {
            return $this->moduleRepo->getStatistics();
        });
    }

    /**
     * Override clearAll to clear all known module cache keys
     */
    protected function clearAll(): bool
    {
        $keys = [
            'all',
            'all_with_children',
            'parents',
            'active',
            'visible',
            'visible_with_children',
            'grouped_modules',
            'statistics'
        ];

        // Also clear group-specific caches
        // Get group names directly from database (not from cache!)
        $groupNames = \DB::table('modules')
            ->select('group_name')
            ->distinct()
            ->whereNotNull('group_name')
            ->pluck('group_name');

        foreach ($groupNames as $groupName) {
            $keys[] = "group:{$groupName}";
        }

        return $this->forgetMany($keys);
    }

    /**
     * Clear all module cache
     */
    public function clearCache(): bool
    {
        return $this->clearAll();
    }

    /**
     * Clear specific module cache
     */
    public function clearModuleCache($id): bool
    {
        return $this->delete("module:{$id}");
    }

    /**
     * Refresh all cache (clear and reload)
     */
    public function refreshCache(): void
    {
        // Clear all cache first
        $this->clearAll();

        // Reload all cache
        $this->getAll();
        $this->getAllWithChildren();
        $this->getParents();
        $this->getActive();
        $this->getVisible();
        $this->getVisibleWithChildren();
        $this->getGroupedModules();
        $this->getStatistics();
    }

    /**
     * Refresh specific module cache
     */
    public function refreshModuleCache($id): void
    {
        // Clear module cache
        $this->clearModuleCache($id);

        // Reload module cache
        $this->findById($id);

        // Also refresh related caches
        $this->delete('all');
        $this->delete('all_with_children');
        $this->delete('parents');
        $this->delete('active');
        $this->delete('visible');
        $this->delete('visible_with_children');
        $this->delete('grouped_modules');
        $this->delete('statistics');

        // Reload them
        $this->getAll();
        $this->getAllWithChildren();
        $this->getParents();
        $this->getActive();
        $this->getVisible();
        $this->getVisibleWithChildren();
        $this->getGroupedModules();
        $this->getStatistics();
    }
}
