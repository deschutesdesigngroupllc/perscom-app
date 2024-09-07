<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sentry;
use Sentry\State\Scope;
use Symfony\Component\HttpFoundation\Response;

class SentryContext
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->bound('sentry')) {
            $guard = match (Filament::getCurrentPanel()?->getId()) {
                'app' => 'web',
                'admin' => 'admin',
                default => null,
            };

            if (Auth::guard($guard)->check()) {
                Sentry\configureScope(function (Scope $scope): void {
                    global $guard;

                    $scope->setUser([
                        'id' => Auth::guard($guard)->user()->getAuthIdentifier(),
                        'username' => Auth::user()->name,
                        'email' => Auth::user()->email,
                    ]);
                });
            }

            if (tenant()) {
                Sentry\configureScope(function (Scope $scope): void {
                    $scope->setTag('tenant', (string) tenant()->getTenantKey());
                    $scope->setContext('tenant', [
                        'ID' => tenant()->getTenantKey(),
                        'Name' => tenant('name'),
                        'Email' => tenant('email'),
                    ]);
                });
            }

            if ($request->routeIs('api.*')) {
                Sentry\configureScope(function (Scope $scope): void {
                    $scope->setTag('api', 'true');
                });
            }
        }

        return $next($request);
    }
}
