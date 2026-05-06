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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\User::observe(\App\Observers\GeneralObserver::class);
        \App\Models\Inventario::observe(\App\Observers\GeneralObserver::class);
        \App\Models\Billing::observe(\App\Observers\GeneralObserver::class);
        \App\Models\Maintenance::observe(\App\Observers\GeneralObserver::class);
    }
}
