<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Cache;

abstract class CacheRepository
{
    /**
     * Cache prefix for this repository
     */
    protected string $cachePrefix = 'cache';

    /**
     * Default cache TTL in seconds (1 hour)
     */
    protected int $cacheTTL = 3600;

    /**
     * Cache store (use default from config)
     */
    protected ?string $cacheStore = null;

    /**
     * Get cache instance
     */
    protected function cache()
    {
        // Use default cache store from config (file, redis, memcached, etc)
        return $this->cacheStore ? Cache::store($this->cacheStore) : Cache::store();
    }

    /**
     * Get cache key with prefix
     */
    protected function getCacheKey(string $key): string
    {
        return "{$this->cachePrefix}:{$key}";
    }

    /**
     * Get value from cache
     */
    protected function get(string $key)
    {
        $cacheKey = $this->getCacheKey($key);
        return $this->cache()->get($cacheKey);
    }

    /**
     * Set value in cache
     */
    protected function set(string $key, $value, ?int $ttl = null): bool
    {
        $cacheKey = $this->getCacheKey($key);
        $ttl = $ttl ?? $this->cacheTTL;

        return $this->cache()->put($cacheKey, $value, $ttl);
    }

    /**
     * Delete value from cache
     */
    protected function delete(string $key): bool
    {
        $cacheKey = $this->getCacheKey($key);
        return $this->cache()->forget($cacheKey);
    }

    /**
     * Clear all cache with this prefix
     * Note: This is overridden in child classes with specific keys
     */
    protected function clearAll(): bool
    {
        // Child classes should implement specific cache clearing
        return true;
    }

    /**
     * Forget multiple keys
     */
    protected function forgetMany(array $keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
        return true;
    }

    /**
     * Check if key exists in cache
     */
    protected function exists(string $key): bool
    {
        $cacheKey = $this->getCacheKey($key);
        return $this->cache()->has($cacheKey);
    }

    /**
     * Remember value in cache (get or set)
     */
    protected function remember(string $key, callable $callback, ?int $ttl = null)
    {
        $cacheKey = $this->getCacheKey($key);
        $ttl = $ttl ?? $this->cacheTTL;

        return $this->cache()->remember($cacheKey, $ttl, $callback);
    }
}
