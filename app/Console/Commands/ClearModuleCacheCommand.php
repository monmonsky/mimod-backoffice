<?php

namespace App\Console\Commands;

use App\Repositories\Cache\ModuleCacheRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearModuleCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-modules {--all : Clear all cache} {--refresh : Clear and reload cache}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear module cache';

    protected ModuleCacheRepository $moduleCacheRepo;

    /**
     * Create a new command instance.
     */
    public function __construct(ModuleCacheRepository $moduleCacheRepo)
    {
        parent::__construct();
        $this->moduleCacheRepo = $moduleCacheRepo;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('all')) {
            // Clear all cache
            $this->info('Clearing all cache...');
            Cache::flush();
            $this->info('✓ All cache cleared successfully.');
            return 0;
        }

        if ($this->option('refresh')) {
            // Clear and reload module cache
            $this->info('Refreshing module cache...');
            $this->moduleCacheRepo->refreshCache();
            $this->info('✓ Module cache refreshed successfully.');
            return 0;
        }

        // Clear only module cache
        $this->info('Clearing module cache...');
        $result = $this->moduleCacheRepo->clearCache();

        if ($result) {
            $this->info('✓ Module cache cleared successfully.');
        } else {
            $this->warn('⚠ Failed to clear module cache or no cache found.');
        }

        return 0;
    }
}
