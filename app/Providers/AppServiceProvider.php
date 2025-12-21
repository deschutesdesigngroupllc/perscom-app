<?php

declare(strict_types=1);

namespace App\Providers;

use App\Console\Commands\BackupCommand;
use App\Dispatchers\Bus\Dispatcher;
use App\Filament\App\Pages\Dashboard;
use App\Http\Middleware\InitializeTenancyBySubdomain;
use App\Models\Admin;
use App\Models\PassportClient;
use App\Models\PassportToken;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use App\Policies\CustomReportPolicy;
use App\Services\ApiPermissionService;
use App\Services\UserSettingsService;
use App\Settings\OrganizationSettings;
use App\Support\Backup\TenantTemporaryDirectory;
use App\Support\Orion\ComponentsResolver;
use App\Support\Orion\KeyResolver;
use App\Support\Passport\AccessToken;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\ExportAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Field;
use Filament\Infolists\Components\Entry;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Bus\Dispatcher as BusDispatcher;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Cashier\Cashier;
use Laravel\Passport\Passport;
use Laravel\Socialite\Contracts\Factory;
use Orion\Contracts\KeyResolver as KeyResolverContract;
use Orion\Drivers\Standard\ComponentsResolver as ComponentsResolverContract;
use Padmission\DataLens\Models\CustomReport;
use Spatie\Backup\Commands\BackupCommand as BaseBackupCommand;
use Spatie\Backup\Config\Config;

use function tenant;

class AppServiceProvider extends ServiceProvider
{
    /**
     * @throws BindingResolutionException
     */
    public function register(): void
    {
        App::make('router')->middlewareGroup('filament.actions', ['auth', 'web', InitializeTenancyBySubdomain::class]);

        Cashier::useCustomerModel(Tenant::class);
        Cashier::useSubscriptionModel(Subscription::class);

        Passport::$cookie = 'perscom_api_key';
        Passport::$decryptsCookies = false;
        Passport::enablePasswordGrant();
        Passport::enableImplicitGrant();
        Passport::ignoreRoutes();
        Passport::tokensCan(ApiPermissionService::scopes());
        Passport::useAccessTokenEntity(AccessToken::class);
        Passport::useTokenModel(PassportToken::class);
        Passport::useClientModel(PassportClient::class);
        Passport::authorizationView(fn (array $parameters) => Inertia::render('passport/Authorize', [
            'client' => $parameters['client']->id,
            'description' => $parameters['client']->description,
            'image' => $parameters['client']->image?->image_url,
            'name' => $parameters['client']->name,
            'scopes' => $parameters['scopes'],
            'state' => $parameters['request']->state,
            'authToken' => $parameters['authToken'],
            'csrfToken' => csrf_token(),
            'tenant' => tenant('slug'),
        ]));

        /**
         * A custom event dispatcher that allows events to be dispatched, or stopped based on some
         * custom logic. We used it to stop notifications from being sent if a certain HTTP header
         * is present.
         */
        $this->app->extend(BusDispatcher::class, fn ($dispatcher, $app): Dispatcher => new Dispatcher($app, $dispatcher));

        /**
         * This API key resolver modifies how the resource IDs are resolved in the HTTP request path
         * different from how the API package would normally resolve them.
         */
        $this->app->bind(KeyResolverContract::class, fn (): KeyResolver => new KeyResolver);

        /**
         * This custom component resolver for the API is used to resolve API HTTP resources based
         * on the requested API version using a versioned folder structure for identifying which
         * HTTP resources belong with which API Version.
         */
        $this->app->bind(ComponentsResolverContract::class, fn ($app, $params): ComponentsResolver => new ComponentsResolver(resourceModelClass: data_get($params, 'resourceModelClass')));

        /**
         * Tenant DB's are backed up using a batching of individual jobs - one for each tenant. Because
         * of this, we need to customize the temporary directory for each tenant job so that it is not
         * overwritten by another job.
         */
        $this->app->bind('backup-temporary-project', fn (): TenantTemporaryDirectory => new TenantTemporaryDirectory);

        /**
         * Because we back up using two different MySQL connections, one for tenant, and one for central,
         * this allows us to rebind the config with the new DB connection values before running the
         * actual back up command.
         */
        $this->app->bind(BaseBackupCommand::class, fn ($app): BackupCommand => new BackupCommand($app->make(Config::class)));
    }

    /**
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        App::macro('isAdmin', fn (): bool => config('tenancy.enabled') && collect(config('tenancy.central_domains'))->contains(request()->getHost()));
        App::macro('isDemo', fn () => $this->app->environment('demo'));

        Auth::viaRequest('api', static fn () => Auth::guard('jwt')->user() ?? Auth::guard('passport')->user());

        $authenticationRedirect = fn (): string => match (App::isAdmin()) {
            true => route('filament.admin.pages.dashboard'),
            default => route('filament.app.pages.dashboard', [
                'tenant' => tenant(),
            ])
        };

        Authenticate::redirectUsing($authenticationRedirect);
        AuthenticateSession::redirectUsing($authenticationRedirect);

        Column::configureUsing(function (Column $field) {
            $closure = match ($field->getName()) {
                'created_at' => function (Column $field): void {
                    /** @var TextColumn $field */
                    $field
                        ->label('Created')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->since()
                        ->dateTimeTooltip()
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
                'updated_at' => function (Column $field): void {
                    /** @var TextColumn $field */
                    $field
                        ->label('Updated')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->since()
                        ->dateTimeTooltip()
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
                default => $field->toggleable(),
            };

            return value($closure, $field);
        });

        DB::prohibitDestructiveCommands(App::isProduction() && ! App::runningConsoleCommand('perscom:install'));

        Entry::configureUsing(function (Entry $field) {
            $closure = match ($field->getName()) {
                'created_at' => function (Entry $field): void {
                    /** @var TextEntry $field */
                    $field
                        ->label('Created')
                        ->since()
                        ->dateTimeTooltip()
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
                'updated_at' => function (Entry $field): void {
                    /** @var TextEntry $field */
                    $field
                        ->label('Updated')
                        ->since()
                        ->dateTimeTooltip()
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

        ExportAction::configureUsing(fn (ExportAction $action): ExportAction => $action->fileDisk('s3'));

        Field::configureUsing(function (Field $field) {
            $closure = match ($field->getName()) {
                'created_at' => function (Field $field): void {
                    $field->label('Created');

                    if ($field instanceof DateTimePicker) {
                        $field->required()
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
                    }
                },
                'updated_at' => function (Field $field): void {
                    $field->label('Updated');

                    if ($field instanceof DateTimePicker) {
                        $field->required()
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
                    }
                },
                default => null,
            };

            return value($closure, $field);
        });

        FilamentView::registerRenderHook(
            name: PanelsRenderHook::BODY_START,
            hook: fn (): \Illuminate\Contracts\View\Factory|View => view('filament.render-hooks.body-start.demo-banner')
        );

        FilamentView::registerRenderHook(
            name: PanelsRenderHook::BODY_START,
            hook: fn (): \Illuminate\Contracts\View\Factory|View|null => config('cashier.secret')
                ? view('filament.render-hooks.body-start.subscription-banner')
                : null,
        );

        FilamentView::registerRenderHook(
            name: PanelsRenderHook::BODY_START,
            hook: fn (): \Illuminate\Contracts\View\Factory|View => view('filament.render-hooks.body-start.announcement-banner')
        );

        FilamentView::registerRenderHook(
            name: PanelsRenderHook::PAGE_START,
            hook: fn (): \Illuminate\Contracts\View\Factory|View => view('filament.render-hooks.page-start.alert-banner'),
            scopes: Dashboard::class,
        );

        Gate::define('viewPulse', fn (Admin|User|null $user = null): bool => $user instanceof Admin);

        Gate::policy(CustomReport::class, CustomReportPolicy::class);
        Gate::before(function (Admin|User|null $user, string $ability, $model) {
            if ($user instanceof Admin) {
                return true;
            }

            if ($user?->hasRole(Utils::getSuperAdminName()) && ! request()->routeIs('api.*')) {
                return true;
            }
        });

        Model::unguard();

        Number::macro('percentageDifference', function (int $oldValue, int $newValue): int|float {
            if ($oldValue === 0) {
                return $newValue > 0
                    ? 100
                    : 0;
            }

            return (($newValue - $oldValue) / $oldValue) * 100;
        });

        $socialite = $this->app->make(Factory::class);
        $socialite->extend('discord', fn () => $socialite->buildProvider(DiscordSocialiteProvider::class, config('services.discord')));

        Str::macro('camelToLower', fn ($value) => Str::lower(preg_replace_callback(
            '/([a-z])([A-Z])/',
            fn ($matches): string => $matches[1].' '.$matches[2], (string) $value)));

        Table::configureUsing(function (Table $table): void {
            $table->defaultSort('created_at', 'desc');
        });

        URL::forceHttps(App::isProduction());
    }
}
