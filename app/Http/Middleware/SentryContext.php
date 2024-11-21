<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Exception;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Sentry;
use Sentry\State\Scope;
use Symfony\Component\HttpFoundation\Response;

class SentryContext
{
    /**
     * @throws Exception
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->bound('sentry')) {
            $guard = match (Filament::getCurrentPanel()?->getId()) {
                'app' => 'web',
                'admin' => 'admin',
                default => null,
            };

            Sentry\configureScope(function (Scope $scope) use ($request): void {
                $scope->setTag('request.route', (string) Route::currentRouteName());
                $scope->setTag('request.method', $request->method());
            });

            if (Auth::guard($guard)->check()) {
                Sentry\configureScope(function (Scope $scope) use ($guard): void {
                    $scope->setUser([
                        'id' => Auth::guard($guard)->user()->getAuthIdentifier(),
                        'name' => Auth::user()->name,
                        'email' => Auth::user()->email,
                    ]);
                });
            }

            if (tenant()) {
                Sentry\configureScope(function (Scope $scope): void {
                    $scope->setTag('tenant.id', (string) tenant()->getTenantKey());
                    $scope->setTag('tenant.name', (string) tenant('name'));
                    $scope->setTag('tenant.email', (string) tenant('email'));
                    $scope->setTag('tenant.slug', (string) tenant('slug'));

                    $scope->setContext('Tenant', [
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

            if ($request->header('X-Perscom-Widget') === 'true') {
                Sentry\configureScope(function (Scope $scope): void {
                    $scope->setTag('widget', 'true');
                });
            }
        }

        return $next($request);
    }
}
