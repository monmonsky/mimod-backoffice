<?php

namespace App\Repositories\Cache;

use App\Repositories\CacheRepository;
use App\Repositories\Contracts\ModuleRepositoryInterface;

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
            'visible_with_children'
        ];

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

        // Reload them
        $this->getAll();
        $this->getAllWithChildren();
        $this->getParents();
        $this->getActive();
        $this->getVisible();
        $this->getVisibleWithChildren();
    }
}
