<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearMenuCache extends Command
{
    protected $signature = 'menu:clear-cache {location?}';
    protected $description = 'Clear menu cache for specific location or all locations';

    public function handle()
    {
        $location = $this->argument('location');

        if ($location) {
            // Clear specific location
            Cache::forget("menu_tree_{$location}");
            Cache::forget("menu_flat_{$location}");
            Cache::forget("menu_items_{$location}");

            $this->info("Menu cache cleared for location: {$location}");
        } else {
            // Clear all menu caches
            $locations = ['header', 'footer', 'sidebar', 'mobile'];

            foreach ($locations as $loc) {
                Cache::forget("menu_tree_{$loc}");
                Cache::forget("menu_flat_{$loc}");
                Cache::forget("menu_items_{$loc}");
            }

            Cache::forget('menu_parents');

            // If using cache tags
            if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
                Cache::tags(['menus'])->flush();
            }

            $this->info('All menu caches cleared successfully!');
        }

        return 0;
    }
}
