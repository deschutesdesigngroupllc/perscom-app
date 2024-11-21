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
use App\Services\UserSettingsService;
use App\Settings\OrganizationSettings;
use App\Support\Backup\TenantTemporaryDirectory;
use App\Support\Orion\ComponentsResolver;
use App\Support\Orion\KeyResolver;
use App\Support\Passport\AccessToken;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Forms\Components\Field;
use Filament\Infolists\Components\Entry;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Bus\Dispatcher as BusDispatcher;
use Illuminate\Contracts\Container\BindingResolutionException;
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
use Laravel\Socialite\Contracts\Factory;
use Orion\Contracts\KeyResolver as KeyResolverContract;
use Orion\Drivers\Standard\ComponentsResolver as ComponentsResolverContract;
use Spatie\Backup\Contracts\TemporaryDirectory;

use function tenant;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Cashier::useCustomerModel(Tenant::class);
        Cashier::useSubscriptionModel(Subscription::class);

        Passport::enablePasswordGrant();
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
        $this->app->bind(TemporaryDirectory::class, fn () => new TenantTemporaryDirectory);
    }

    /**
     * @throws BindingResolutionException
     */
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
            $closure = match ($field->getName()) {
                'created_at' => function (Column $field) {
                    /** @var TextColumn $field */
                    $field
                        ->label('Created')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->dateTime()
                        ->timezone(function () {
                            if (! tenancy()->initialized) {
                                return config('app.timezone');
                            }

                            return UserSettingsService::get('timezone', function () {
                                /** @var OrganizationSettings $settings */
                                $settings = app(OrganizationSettings::class);

                                return $settings->timezone ?? config('app.timezone');
                            });
                        });
                },
                'updated_at' => function (Column $field) {
                    /** @var TextColumn $field */
                    $field
                        ->label('Updated')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->dateTime()
                        ->timezone(function () {
                            if (! tenancy()->initialized) {
                                return config('app.timezone');
                            }

                            return UserSettingsService::get('timezone', function () {
                                /** @var OrganizationSettings $settings */
                                $settings = app(OrganizationSettings::class);

                                return $settings->timezone ?? config('app.timezone');
                            });
                        });
                },
                'deleted_at' => function (Column $field) {
                    /** @var TextColumn $field */
                    $field
                        ->label('Deleted')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->dateTime()
                        ->timezone(function () {
                            if (! tenancy()->initialized) {
                                return config('app.timezone');
                            }

                            return UserSettingsService::get('timezone', function () {
                                /** @var OrganizationSettings $settings */
                                $settings = app(OrganizationSettings::class);

                                return $settings->timezone ?? config('app.timezone');
                            });
                        });
                },
                default => null,
            };

            return value($closure, $field);
        });

        Entry::configureUsing(function (Entry $field) {
            $closure = match ($field->getName()) {
                'created_at' => function (Entry $field) {
                    /** @var TextEntry $field */
                    $field
                        ->label('Created')
                        ->dateTime()
                        ->timezone(function () {
                            if (! tenancy()->initialized) {
                                return config('app.timezone');
                            }

                            return UserSettingsService::get('timezone', function () {
                                /** @var OrganizationSettings $settings */
                                $settings = app(OrganizationSettings::class);

                                return $settings->timezone ?? config('app.timezone');
                            });
                        });
                },
                'updated_at' => function (Entry $field) {
                    /** @var TextEntry $field */
                    $field
                        ->label('Updated')
                        ->dateTime()
                        ->timezone(function () {
                            if (! tenancy()->initialized) {
                                return config('app.timezone');
                            }

                            return UserSettingsService::get('timezone', function () {
                                /** @var OrganizationSettings $settings */
                                $settings = app(OrganizationSettings::class);

                                return $settings->timezone ?? config('app.timezone');
                            });
                        });
                },
                'deleted_at' => function (Entry $field) {
                    /** @var TextEntry $field */
                    $field
                        ->label('Deleted')
                        ->dateTime()
                        ->timezone(function () {
                            if (! tenancy()->initialized) {
                                return config('app.timezone');
                            }

                            return UserSettingsService::get('timezone', function () {
                                /** @var OrganizationSettings $settings */
                                $settings = app(OrganizationSettings::class);

                                return $settings->timezone ?? config('app.timezone');
                            });
                        });
                },
                default => null,
            };

            return value($closure, $field);
        });

        Feature::discover();
        Feature::resolveScopeUsing(static fn ($driver) => tenant());

        FilamentAsset::register([
            AlpineComponent::make('widget-code-generator', __DIR__.'/../../resources/js/dist/components/widget-code-generator/index.js'),
        ]);

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
            name: PanelsRenderHook::BODY_START,
            hook: fn () => view('filament.render-hooks.body-start.demo-banner')
        );

        FilamentView::registerRenderHook(
            name: PanelsRenderHook::BODY_START,
            hook: fn () => view('filament.render-hooks.body-start.subscription-banner'),
        );

        FilamentView::registerRenderHook(
            name: PanelsRenderHook::BODY_START,
            hook: fn () => view('filament.render-hooks.body-start.announcement-banner')
        );

        FilamentView::registerRenderHook(
            name: PanelsRenderHook::PAGE_START,
            hook: fn () => view('filament.render-hooks.page-start.alert-banner'),
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

        $socialite = $this->app->make(Factory::class);
        $socialite->extend('discord', function () use ($socialite) {
            return $socialite->buildProvider(DiscordSocialiteProvider::class, config('services.discord'));
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
    }
}
