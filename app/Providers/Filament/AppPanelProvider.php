<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Features\BillingFeature;
use App\Filament\App\Pages\Dashboard;
use App\Filament\App\Resources\AnnouncementResource\Widgets\RecentAnnouncements;
use App\Filament\App\Resources\AssignmentRecordResource;
use App\Filament\App\Resources\AwardRecordResource;
use App\Filament\App\Resources\CombatRecordResource;
use App\Filament\App\Resources\EventResource;
use App\Filament\App\Resources\QualificationRecordResource;
use App\Filament\App\Resources\RankRecordResource;
use App\Filament\App\Resources\ServiceRecordResource;
use App\Filament\App\Resources\UserResource;
use App\Filament\App\Resources\UserResource\Widgets\UsersOverview;
use App\Filament\App\Widgets\AccountWidget;
use App\Filament\App\Widgets\OrganizationInfoWidget;
use App\Http\Middleware\CaptureUserOnlineStatus;
use App\Http\Middleware\CheckUserApprovalStatus;
use App\Http\Middleware\InitializeTenancyBySubdomain;
use App\Http\Middleware\SentryContext;
use App\Models\Tenant;
use Awcodes\FilamentQuickCreate\QuickCreatePlugin;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Provider;
use Filament\FontProviders\SpatieGoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\MinimalTheme;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Laravel\Pennant\Feature;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('app')
            ->login()
            ->registration()
            ->passwordReset()
            ->emailVerification()
            ->profile(isSimple: false)
            ->colors([
                'primary' => Color::Blue,
            ])
            ->font('Inter', provider: SpatieGoogleFontProvider::class)
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
            ->discoverClusters(in: app_path('Filament/App/Clusters'), for: 'App\\Filament\\App\\Clusters')
            ->widgets([
                OrganizationInfoWidget::class,
                AccountWidget::class,
                UsersOverview::class,
                RecentAnnouncements::class,
            ])
            ->middleware([
                InitializeTenancyBySubdomain::class,
                SentryContext::class,
            ], isPersistent: true)
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                CaptureUserOnlineStatus::class,
                CheckUserApprovalStatus::class,
                PreventAccessFromCentralDomains::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->maxContentWidth(MaxWidth::Full)
            ->viteTheme('resources/css/filament/app/theme.css')
            ->brandName('PERSCOM')
            ->brandLogo(fn () => view('components.logo'))
            ->plugins([
                new MinimalTheme,
                FilamentShieldPlugin::make(),
                QuickCreatePlugin::make()
                    ->renderUsingHook(PanelsRenderHook::GLOBAL_SEARCH_AFTER)
                    ->alwaysShowModal()
                    ->includes([
                        AssignmentRecordResource::class,
                        AwardRecordResource::class,
                        CombatRecordResource::class,
                        EventResource::class,
                        QualificationRecordResource::class,
                        RankRecordResource::class,
                        ServiceRecordResource::class,
                        UserResource::class,
                    ])
                    ->slideOver(),
                FilamentSocialitePlugin::make()
                    ->registration()
                    ->providers([
                        Provider::make('google')
                            ->label('Google')
                            ->icon('fab-google')
                            ->outlined(false),
                        Provider::make('discord')
                            ->label('Discord')
                            ->icon('fab-discord')
                            ->outlined(false),
                        Provider::make('github')
                            ->label('GitHub')
                            ->icon('fab-github')
                            ->outlined(false),
                    ]),
            ])
            ->databaseNotifications()
            ->sidebarCollapsibleOnDesktop()
            ->userMenuItems([
                MenuItem::make()
                    ->label('Billing')
                    ->url(fn () => route('spark.portal'), shouldOpenInNewTab: true)
                    ->visible(fn () => Feature::active(BillingFeature::class))
                    ->icon('heroicon-o-currency-dollar'),
                MenuItem::make()
                    ->label('Documentation')
                    ->url('https://docs.perscom.io', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-book-open'),
                MenuItem::make()
                    ->label('Feedback')
                    ->url('https://feedback.perscom.io', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-face-smile'),
                MenuItem::make()
                    ->label('System Status')
                    ->url('https://status.perscom.io', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-command-line'),
            ])
            ->tenantMenu(false)
            ->tenant(Tenant::class, 'slug')
            ->tenantDomain('{tenant:slug}'.config('app.base_url'))
            ->tenantBillingProvider(new BillingProvider)
            ->requiresTenantSubscription();
    }
}
