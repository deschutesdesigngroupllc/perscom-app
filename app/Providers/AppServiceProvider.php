<?php

namespace App\Providers;

use App\Models\Tenant;
use App\Observers\TenantObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Spatie\Permission\PermissionRegistrar;
use Stancl\Tenancy\Events\TenancyBootstrapped;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Request::macro('isCentralRequest', function () {
            return collect(config('tenancy.central_domains'))->contains(\request()->getHost());
        });

        if (app()->environment() === 'local') {
            config()->set('tenancy.central_domains', ['localhost']);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Prefix the permission cache key with the tenant identifier
        Event::listen(TenancyBootstrapped::class, function (TenancyBootstrapped $event) {
            PermissionRegistrar::$cacheKey = 'spatie.permission.cache.tenant.' . $event->tenancy->tenant->id;
        });
    }
}
