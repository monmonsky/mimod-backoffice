<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind Repository Interfaces to Implementations
        $this->app->bind(
            \App\Repositories\Contracts\UserRepositoryInterface::class,
            \App\Repositories\UserRepository::class
        );

        // Settings Repositories
        $this->app->bind(
            \App\Repositories\Contracts\GeneralSettingsRepositoryInterface::class,
            \App\Repositories\Settings\GeneralSettingsRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PaymentSettingsRepositoryInterface::class,
            \App\Repositories\Settings\PaymentSettingsRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\ShippingSettingsRepositoryInterface::class,
            \App\Repositories\Settings\ShippingSettingsRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\AccessControl\ModuleRepositoryInterface::class,
            \App\Repositories\AccessControl\ModuleRepository::class
        );

        // Module Cache Repository (Singleton for performance)
        $this->app->singleton(
            \App\Repositories\Cache\ModuleCacheRepository::class,
            function ($app) {
                return new \App\Repositories\Cache\ModuleCacheRepository(
                    $app->make(\App\Repositories\Contracts\AccessControl\ModuleRepositoryInterface::class)
                );
            }
        );

        // Access Control Repositories
        $this->app->bind(
            \App\Repositories\Contracts\AccessControl\RoleRepositoryInterface::class,
            \App\Repositories\AccessControl\RoleRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PermissionRepositoryInterface::class,
            \App\Repositories\PermissionRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PermissionGroupRepositoryInterface::class,
            \App\Repositories\PermissionGroupRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register View Composers for both sidebar views
        \Illuminate\Support\Facades\View::composer(
            ['partials.sidebar', 'partials.sidebar-dynamic'],
            \App\Http\ViewComposers\SidebarComposer::class
        );
    }
}
