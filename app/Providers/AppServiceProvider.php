<?php

namespace App\Providers;

use App\Actions\Passport\CreatePersonalAccessToken;
use App\Contracts\Passport\CreatesPersonalAccessToken;
use App\Models\PassportClient;
use App\Models\PassportToken;
use App\Models\Permission;
use App\Models\Tenant;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Laravel\Cashier\Cashier;
use Laravel\Passport\Passport;
use Laravel\Pennant\Feature;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;
use Laravel\Socialite\Contracts\Factory;

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
            if (env('TENANT_TESTING', false)) {
                return false;
            }

            return collect(config('tenancy.central_domains'))->contains(\request()->getHost());
        });

        Request::macro('isDemoMode', function () {
            return \request()->getHost() === \config('tenancy.demo_host') ||
                    (\request()->expectsJson() &&
                     ((\request()->headers->has('X-Perscom-Id') &&
                       \request()->header('X-Perscom-Id') == \config('tenancy.demo_id')) ||
                      (\request()->get('perscom_id') != null &&
                       \request()->get('perscom_id') == \config('tenancy.demo_id'))));
        });

        Cashier::useCustomerModel(Tenant::class);

        Passport::ignoreRoutes();
        Passport::ignoreMigrations();
        Passport::tokensCan(Permission::getPermissionsFromConfig()->toArray());
        Passport::useTokenModel(PassportToken::class);
        Passport::useClientModel(PassportClient::class);
        Passport::authorizationView(function ($parameters) {
            return Inertia::render('passport/Authorize', [
                'client' => $parameters['client']->id,
                'name' => $parameters['client']->name,
                'scopes' => $parameters['scopes'],
                'state' => $parameters['request']->state,
                'authToken' => $parameters['authToken'],
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

        $this->app->bind(CreatesPersonalAccessToken::class, CreatePersonalAccessToken::class);

        Feature::discover();
        Feature::resolveScopeUsing(static fn ($driver) => \tenant('id'));

        EnsureFeaturesAreActive::whenInactive(
            static function ($request, array $features) {
                abort(403, 'The feature you are trying to access is not current enabled for your account.');
            }
        );
    }
}
