<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Http\Middleware\AttachTraceAndRequestId;
use App\Http\Middleware\SentryContext;
use Filament\FontProviders\SpatieGoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\MinimalTheme;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->domain(config('app.url'))
            ->login()
            ->emailVerification()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->font('Inter', provider: SpatieGoogleFontProvider::class)
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->databaseNotifications()
            ->navigationItems([
                NavigationItem::make('Health')
                    ->icon('heroicon-o-heart')
                    ->group('Tools')
                    ->sort(3)
                    ->url(config('app.admin_url').'/admin/health', shouldOpenInNewTab: true),
                NavigationItem::make('Horizon')
                    ->icon('heroicon-o-queue-list')
                    ->group('Tools')
                    ->sort(3)
                    ->url(config('app.admin_url').'/admin/horizon', shouldOpenInNewTab: true),
                NavigationItem::make('Pulse')
                    ->url(config('app.admin_url').'/admin/pulse', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-heart')
                    ->group('Tools')
                    ->sort(3),
                NavigationItem::make('Telescope')
                    ->url(config('app.admin_url').'/admin/telescope', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-presentation-chart-line')
                    ->group('Tools')
                    ->sort(3),
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->authGuard('admin')
            ->maxContentWidth(MaxWidth::Full)
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->brandName('PERSCOM')
            ->brandLogo(fn () => view('components.logo'))
            ->plugins([
                new MinimalTheme,
            ]);
    }
}
