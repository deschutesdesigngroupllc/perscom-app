<?php

namespace App\Providers;

use App\Models\Passport\Client;
use App\Models\Passport\Token;
use App\Models\Permission;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Laravel\Passport\RouteRegistrar;
use Laravel\Socialite\Contracts\Factory;
use Spatie\Permission\PermissionRegistrar;
use Stancl\Tenancy\Events\TenancyBootstrapped;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

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

        Passport::ignoreMigrations();
        Passport::tokensCan(Permission::getPermissionsFromConfig()->toArray());
        Passport::useTokenModel(Token::class);
        Passport::useClientModel(Client::class);
        Passport::routes(
            static function (RouteRegistrar $router) {
                $router->forAccessTokens();
                $router->forTransientTokens();
                $router->forClients();
                $router->forPersonalAccessTokens();
            },
            [
                'middleware' => [InitializeTenancyByDomainOrSubdomain::class, PreventAccessFromCentralDomains::class],
            ]
        );
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

        Event::listen(TenancyBootstrapped::class, function (TenancyBootstrapped $event) {
            PermissionRegistrar::$cacheKey = 'spatie.permission.cache.tenant.' . $event->tenancy->tenant->id;
        });
    }
}
