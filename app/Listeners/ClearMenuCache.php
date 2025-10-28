<?php

namespace App\Listeners;

use App\Events\MenuUpdated;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ClearMenuCache
{
    /**
     * Handle the event.
     */
    public function handle(MenuUpdated $event): void
    {
        // Clear all menu-related cache
        $this->clearMenuCache();

        // Log the cache clear action
        Log::info('Menu cache cleared', [
            'action' => $event->action,
            'menu_id' => $event->menuId,
        ]);
    }

    /**
     * Clear all menu cache patterns
     */
    private function clearMenuCache(): void
    {
        // Clear cache for all menu locations
        $locations = ['header', 'footer', 'sidebar', 'mobile'];

        foreach ($locations as $location) {
            Cache::forget("menu_tree_{$location}");
            Cache::forget("menu_flat_{$location}");
            Cache::forget("menu_items_{$location}");
        }

        // Clear parent menus cache
        Cache::forget('menu_parents');

        // Clear all menu cache if using cache tags (for Redis/Memcached)
        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            Cache::tags(['menus'])->flush();
        }
    }
}
