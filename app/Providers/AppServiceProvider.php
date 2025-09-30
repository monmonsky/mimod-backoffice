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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
