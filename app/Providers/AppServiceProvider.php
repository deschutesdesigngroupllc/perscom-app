<?php

declare(strict_types=1);

namespace App\Providers;

use App\Dispatchers\Bus\Dispatcher;
use App\Filament\App\Pages\Dashboard;
use App\Models\Admin;
use App\Models\PassportClient;
use App\Models\PassportToken;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use App\Services\ApiPermissionService;
use App\Support\Orion\ComponentsResolver;
use App\Support\Orion\KeyResolver;
use App\Support\Passport\AccessToken;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Forms\Components\Field;
use Filament\Infolists\Components\Entry;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Bus\Dispatcher as BusDispatcher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Cashier\Cashier;
use Laravel\Passport\Passport;
use Laravel\Pennant\Feature;
use Orion\Contracts\KeyResolver as KeyResolverContract;
use Orion\Drivers\Standard\ComponentsResolver as ComponentsResolverContract;

use function tenant;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Cashier::useCustomerModel(Tenant::class);
        Cashier::useSubscriptionModel(Subscription::class);

        Passport::enableImplicitGrant();
        Passport::ignoreRoutes();
        Passport::tokensCan(ApiPermissionService::scopes());
        Passport::useAccessTokenEntity(AccessToken::class);
        Passport::useTokenModel(PassportToken::class);
        Passport::useClientModel(PassportClient::class);
        Passport::authorizationView(function ($parameters) {
            return Inertia::render('passport/Authorize', [
                'client' => $parameters['client']->id,
                'description' => $parameters['client']->description,
                'image' => $parameters['client']->image?->image_url,
                'name' => $parameters['client']->name,
                'scopes' => $parameters['scopes'],
                'state' => $parameters['request']->state,
                'authToken' => $parameters['authToken'],
                'csrfToken' => csrf_token(),
                'tenant' => tenant('slug'),
            ]);
        });

        $this->app->extend(BusDispatcher::class, fn ($dispatcher, $app) => new Dispatcher($app, $dispatcher));
        $this->app->bind(KeyResolverContract::class, fn () => new KeyResolver);
        $this->app->bind(ComponentsResolverContract::class, fn ($app, $params) => new ComponentsResolver(resourceModelClass: data_get($params, 'resourceModelClass')));
    }

    public function boot(): void
    {
        App::macro('isAdmin', fn () => collect(config('tenancy.central_domains'))->contains(request()->getHost()));
        App::macro('isDemo', fn () => $this->app->environment('demo'));

        Auth::viaRequest('api', static function () {
            return Auth::guard('jwt')->user() ?? Auth::guard('passport')->user();
        });

        $authenticationRedirect = function () {
            return match (App::isAdmin()) {
                true => route('filament.admin.pages.dashboard'),
                default => route('filament.app.pages.dashboard', [
                    'tenant' => tenant(),
                ])
            };
        };

        Authenticate::redirectUsing($authenticationRedirect);
        AuthenticateSession::redirectUsing($authenticationRedirect);

        Column::configureUsing(function (Column $field) {
            match ($field->getName()) {
                'created_at' => $field->label('Created'),
                'updated_at' => $field->label('Updated'),
                'deleted_at' => $field->label('Deleted'),
                default => null,
            };
        });

        Entry::configureUsing(function (Entry $field) {
            match ($field->getName()) {
                'created_at' => $field->label('Created'),
                'updated_at' => $field->label('Updated'),
                'deleted_at' => $field->label('Deleted'),
                default => null,
            };
        });

        Feature::discover();
        Feature::resolveScopeUsing(static fn ($driver) => tenant());

        FilamentShield::configurePermissionIdentifierUsing(function ($resource) {
            return Str::of($resource)
                ->afterLast('Resources\\')
                ->before('Resource')
                ->replace('\\', '')
                ->lower()
                ->replace('_', '')
                ->toString();
        });

        Field::configureUsing(function (Field $field) {
            match ($field->getName()) {
                'created_at' => $field->label('Created'),
                'updated_at' => $field->label('Updated'),
                'deleted_at' => $field->label('Deleted'),
                default => null,
            };
        });

        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_START,
            fn () => view('filament.render-hooks.body-start.demo-banner')
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_START,
            fn () => view('filament.render-hooks.body-start.subscription-banner')
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_START,
            fn () => view('filament.render-hooks.body-start.announcement-banner')
        );

        FilamentView::registerRenderHook(
            name: PanelsRenderHook::PAGE_START,
            hook: fn () => view('filament.render-hooks.page-start.message-banner'),
            scopes: Dashboard::class,
        );

        Gate::define('viewPulse', function (Admin|User|null $user = null) {
            return $user instanceof Admin;
        });

        Gate::before(function (Admin|User|null $user, string $ability, $model) {
            if ($user instanceof Admin) {
                return true;
            }

            if ($user?->hasRole(Utils::getSuperAdminName()) && ! request()->routeIs('api.*')) {
                return true;
            }
        });

        Str::macro('camelToLower', function ($value) {
            return Str::lower(preg_replace_callback(
                '/([a-z])([A-Z])/',
                function ($matches) {
                    return $matches[1].' '.$matches[2];
                }, $value));
        });

        Table::configureUsing(function (Table $table) {
            $table->defaultSort('created_at', 'desc');
        });

        Model::unguard();
    }
}
