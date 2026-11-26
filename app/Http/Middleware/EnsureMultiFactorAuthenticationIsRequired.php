<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Settings\PermissionSettings;
use Closure;
use Filament\Auth\MultiFactor\Http\Middleware\EnsureMultiFactorAuthenticationIsEnabled;
use Illuminate\Http\Request;

class EnsureMultiFactorAuthenticationIsRequired extends EnsureMultiFactorAuthenticationIsEnabled
{
    public function handle(Request $request, Closure $next): mixed
    {
        /** @var PermissionSettings $settings */
        $settings = resolve(PermissionSettings::class);

        if (! $settings->require_multi_factor_authentication) {
            return $next($request);
        }

        return parent::handle($request, $next);
    }
}
