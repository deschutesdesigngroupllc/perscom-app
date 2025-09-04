<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\App\Pages\Auth\EditProfile;
use App\Filament\App\Pages\Auth\EmailVerificationPrompt;
use App\Filament\App\Pages\Auth\Login;
use App\Filament\App\Pages\Auth\Register;
use App\Filament\App\Pages\Auth\RequestPasswordReset;
use App\Filament\App\Pages\Dashboard;
use App\Filament\App\Resources\AnnouncementResource\Widgets\RecentAnnouncements;
use App\Filament\App\Resources\UserResource\Widgets\UsersOverview;
use App\Filament\App\Widgets\AccountWidget;
use App\Filament\App\Widgets\OrganizationInfoWidget;
use App\Http\Middleware\AttachTraceAndRequestId;
use App\Http\Middleware\CaptureUserOnlineStatus;
use App\Http\Middleware\CheckUserApprovalStatus;
use App\Http\Middleware\InitializeTenancyBySubdomain;
use App\Http\Middleware\RedirectSocialProvider;
use App\Http\Middleware\SentryContext;
use App\Models\SocialiteUser;
use App\Models\Tenant;
use Archilex\AdvancedTables\Plugin\AdvancedTablesPlugin;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use DutchCodingCompany\FilamentSocialite\Exceptions\ImplementationException;
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Provider;
use Filament\Actions\Action;
use Filament\FontProviders\SpatieGoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use FilamentThemes\Minimal\Theme;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

class AppPanelProvider extends PanelProvider
{
    /**
     * @throws ImplementationException
     */
    public function panel(Panel $panel): Panel
    {
        $registration = $this->app->environment('demo')
            ? null
            : Register::class;

        $passwordReset = $this->app->environment('demo')
            ? null
            : RequestPasswordReset::class;

        $emailVerification = $this->app->environment('demo')
            ? null
            : EmailVerificationPrompt::class;

        $socialProviders = $this->app->environment('demo')
            ? []
            : [
                Provider::make('google')
                    ->label('Google')
                    ->icon('fab-google')
                    ->stateless()
                    ->outlined(false),
                Provider::make('discord')
                    ->label('Discord')
                    ->icon('fab-discord')
                    ->stateless()
                    ->outlined(false),
                Provider::make('github')
                    ->label('GitHub')
                    ->icon('fab-github')
                    ->stateless()
                    ->outlined(false),
            ];

        return $panel
            ->default()
            ->id('app')
            ->login(Login::class)
            ->registration($registration)
            ->passwordReset($passwordReset)
            ->emailVerification($emailVerification)
            ->profile(EditProfile::class, isSimple: false)
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
            ], isPersistent: true)
            ->middleware([
                AttachTraceAndRequestId::class,
                SentryContext::class,
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
                RedirectSocialProvider::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->maxContentWidth(Width::Full)
            ->viteTheme('resources/css/filament/app/theme.css')
            ->brandName('PERSCOM')
            ->brandLogo(fn () => view('components.logo'))
            ->plugins([
                AdvancedTablesPlugin::make()
                    ->persistActiveViewInSession()
                    ->resourceEnabled(false)
                    ->favoritesBarSize(Size::Small)
                    ->favoritesBarTheme(config('advanced-tables.favorites_bar.theme')),
                Theme::make(),
                FilamentShieldPlugin::make(),
                FilamentSocialitePlugin::make()
                    ->socialiteUserModelClass(SocialiteUser::class)
                    ->registration()
                    ->providers($socialProviders),
            ])
            ->databaseNotifications()
            ->sidebarCollapsibleOnDesktop()
            ->userMenuItems([
                Action::make('billing')
                    ->label('Billing')
                    ->url(fn () => route('spark.portal'), shouldOpenInNewTab: true)
                    ->visible(fn () => Gate::check('billing'))
                    ->icon('heroicon-o-currency-dollar'),
                Action::make('docs')
                    ->label('Documentation')
                    ->url('https://docs.perscom.io', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-book-open'),
                Action::make('support')
                    ->label('Support')
                    ->url('https://perscom.io/slack', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-question-mark-circle'),
                Action::make('status')
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
