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
        // TODO: Uncomment when Settings repositories are created
        // $this->app->bind(
        //     \App\Repositories\Contracts\GeneralSettingsRepositoryInterface::class,
        //     \App\Repositories\Settings\GeneralSettingsRepository::class
        // );

        // $this->app->bind(
        //     \App\Repositories\Contracts\PaymentSettingsRepositoryInterface::class,
        //     \App\Repositories\Settings\PaymentSettingsRepository::class
        // );

        // $this->app->bind(
        //     \App\Repositories\Contracts\ShippingSettingsRepositoryInterface::class,
        //     \App\Repositories\Settings\ShippingSettingsRepository::class
        // );

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

        $this->app->bind(
            \App\Repositories\Contracts\UserActivityRepositoryInterface::class,
            \App\Repositories\AccessControl\UserActivityRepository::class
        );

        // Catalog Repositories
        $this->app->bind(
            \App\Repositories\Contracts\Catalog\CategoryRepositoryInterface::class,
            \App\Repositories\Catalog\CategoryRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\Catalog\BrandRepositoryInterface::class,
            \App\Repositories\Catalog\BrandRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\Catalog\ProductRepositoryInterface::class,
            \App\Repositories\Catalog\ProductRepository::class
        );

        // Orders Repositories
        $this->app->bind(
            \App\Repositories\Contracts\Orders\OrderRepositoryInterface::class,
            \App\Repositories\Orders\OrderRepository::class
        );

        // Customer Authentication Repository (for frontend customers)
        $this->app->bind(
            \App\Repositories\Contracts\CustomerRepositoryInterface::class,
            \App\Repositories\CustomerRepository::class
        );

        // Customer Address Repository
        $this->app->bind(
            \App\Repositories\Contracts\CustomerAddressRepositoryInterface::class,
            \App\Repositories\CustomerAddressRepository::class
        );

        // Customers Repositories (for backoffice)
        $this->app->bind(
            \App\Repositories\Contracts\Customers\CustomerRepositoryInterface::class,
            \App\Repositories\Customers\CustomerRepository::class
        );

        // Marketing Repositories
        $this->app->bind(
            \App\Repositories\Contracts\Marketing\CouponRepositoryInterface::class,
            \App\Repositories\Marketing\CouponRepository::class
        );

        // Payment Repositories
        $this->app->bind(
            \App\Repositories\Contracts\Payment\PaymentMethodRepositoryInterface::class,
            \App\Repositories\Payment\PaymentMethodRepository::class
        );

        // Shipping Repositories
        $this->app->bind(
            \App\Repositories\Contracts\Shipping\ShippingMethodRepositoryInterface::class,
            \App\Repositories\Shipping\ShippingMethodRepository::class
        );

        // Appearance / Navigation Menu Repositories
        $this->app->bind(
            \App\Repositories\Appearance\Navigation\Contracts\MenuRepositoryInterface::class,
            \App\Repositories\Appearance\Navigation\MenuRepository::class
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
            \App\Http\View\Composers\SidebarComposer::class
        );

        // Laravel Pulse Authorization
        \Laravel\Pulse\Facades\Pulse::user(fn ($request) => $request->user());

        // Pulse Authorization Gate
        \Illuminate\Support\Facades\Gate::define('viewPulse', function ($user = null) {
            // Allow all in local environment
            if (config('app.env') === 'local') {
                return true;
            }

            // In production, require authentication
            if (!$user) {
                return false;
            }

            // Only allow authenticated users (change this for specific roles/emails)
            return true; // All authenticated users can access

            // Or restrict to specific emails:
            // return in_array($user->email, ['admin@example.com', 'dev@example.com']);

            // Or restrict to admin role:
            // return $user->hasRole('admin');
        });
    }
}
