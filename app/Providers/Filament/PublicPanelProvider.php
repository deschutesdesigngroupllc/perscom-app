<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Http\Middleware\AttachTraceAndRequestId;
use App\Http\Middleware\CheckSubscription;
use App\Http\Middleware\InitializeTenancyBySubdomain;
use App\Http\Middleware\PreventAccessFromCentralDomains;
use App\Http\Middleware\SentryContext;
use Filament\FontProviders\SpatieGoogleFontProvider;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class PublicPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('public')
            ->path('public')
            ->domain(config('tenancy.enabled') ? '' : config('app.url'))
            ->colors([
                'primary' => Color::Blue,
            ])
            ->font('Inter', provider: SpatieGoogleFontProvider::class)
            ->discoverPages(in: app_path('Filament/Public/Pages'), for: 'App\\Filament\\Public\\Pages')
            ->when(config('tenancy.enabled'), fn (Panel $panel): Panel => $panel->middleware([
                InitializeTenancyBySubdomain::class,
            ], isPersistent: true))
            ->when(config('tenancy.enabled'), fn (Panel $panel): Panel => $panel->middleware([
                PreventAccessFromCentralDomains::class,
            ]))
            ->middleware([
                CheckSubscription::class,
                AttachTraceAndRequestId::class,
                SentryContext::class,
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([])
            ->topNavigation()
            ->simplePageMaxContentWidth(Width::ExtraLarge)
            ->viteTheme('resources/css/filament/app/theme.css')
            ->brandName('PERSCOM')
            ->brandLogo(fn (): Factory|View => view('components.logo'));
    }
}
