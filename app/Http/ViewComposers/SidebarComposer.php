<?php

namespace App\Http\ViewComposers;

use App\Repositories\Cache\ModuleCacheRepository;
use Illuminate\View\View;

class SidebarComposer
{
    protected $moduleCache;

    public function __construct(ModuleCacheRepository $moduleCache)
    {
        $this->moduleCache = $moduleCache;
    }

    /**
     * Bind data to the view.
     */
    public function compose(View $view)
    {
        // Get all modules with children, ordered by sort_order
        $sidebarModules = $this->moduleCache->getAllWithChildren();

        $view->with('sidebarModules', $sidebarModules);
    }
}
