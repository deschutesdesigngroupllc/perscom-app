<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory;
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

        Request::macro('isDemoMode', function () {
            return \request()->getHost() === env('TENANT_DEMO_HOST', null);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $socialite = $this->app->make(Factory::class);
        $socialite->extend('discord', function () use ($socialite) {
            $config = config('services.discord');
            return $socialite->buildProvider(DiscordSocialiteProvider::class, $config);
        });

        // Prefix the permission cache key with the tenant identifier
        Event::listen(TenancyBootstrapped::class, function (TenancyBootstrapped $event) {
            PermissionRegistrar::$cacheKey = 'spatie.permission.cache.tenant.' . $event->tenancy->tenant->id;
        });
    }
}
