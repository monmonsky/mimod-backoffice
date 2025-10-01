<?php

namespace App\Http\View\Composers;

use App\Repositories\Cache\ModuleCacheRepository;
use Illuminate\View\View;

class SidebarComposer
{
    protected ModuleCacheRepository $moduleCache;

    public function __construct(ModuleCacheRepository $moduleCache)
    {
        $this->moduleCache = $moduleCache;
    }

    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        try {
            $modules = $this->moduleCache->getVisibleWithChildren();
        } catch (\Exception $e) {
            // Fallback to empty array if cache fails
            \Log::warning('Failed to load sidebar modules from cache: ' . $e->getMessage());
            $modules = [];
        }

        $view->with('sidebarModules', $modules ?? []);
    }
}
