<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Settings\PermissionSettings;
use Closure;
use Filament\Auth\MultiFactor\Http\Middleware\EnsureMultiFactorAuthenticationIsEnabled;
use Illuminate\Http\Request;

/**
 * Filament evaluates whether multi-factor authentication is required when routes are
 * registered, before a tenant is known. This middleware defers that decision to the
 * request so each organization can opt in through its permission settings.
 */
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
