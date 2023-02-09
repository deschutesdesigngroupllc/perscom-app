<?php

namespace App\Providers;

use App\Actions\Passport\CreatePersonalAccessToken;
use App\Contracts\Passport\CreatesPersonalAccessToken;
use App\Models\PassportClient;
use App\Models\PassportToken;
use App\Models\Permission;
use App\Models\Tenant;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Laravel\Cashier\Cashier;
use Laravel\Passport\Passport;
use Laravel\Socialite\Contracts\Factory;
use Outl1ne\NovaSettings\NovaSettings;
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
            return \request()->getHost() === env('TENANT_DEMO_HOST', null) ||
                   (\request()->expectsJson() &&
                    (\request()->header('X-Perscom-Id') === env('TENANT_DEMO_ID') ||
                     \request()->get('perscom_id') === env('TENANT_DEMO_ID')));
        });

        Cashier::useCustomerModel(Tenant::class);

        Passport::ignoreRoutes();
        Passport::ignoreMigrations();
        Passport::tokensCan(Permission::getPermissionsFromConfig()->toArray());
        Passport::useTokenModel(PassportToken::class);
        Passport::useClientModel(PassportClient::class);
        Passport::authorizationView(function ($client, $user, $scopes, $request, $authToken) {
            return Inertia::render('passport/Authorize', [
                'client' => $client->id,
                'name' => $client->name,
                'scopes' => $scopes,
                'state' => $request->state,
                'authToken' => $authToken,
                'csrfToken' => csrf_token(),
            ]);
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

        Event::listen(TenancyBootstrapped::class, function (TenancyBootstrapped $event) {
            PermissionRegistrar::$cacheKey = 'spatie.permission.cache.tenant.'.$event->tenancy->tenant->id;
            Config::set('app.timezone', NovaSettings::getSetting('timezone', \config('app.timezone')));
        });

        $this->app->bind(CreatesPersonalAccessToken::class, CreatePersonalAccessToken::class);
    }
}
