<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Context;
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
        if (App::bound('sentry')) {
            $guard = match (true) {
                $request->routeIs('api.*'), $request->routeIs('oauth.*'), $request->routeIs('oidc.*') => 'api',
                App::isAdmin() => 'admin',
                default => 'web',
            };

            Sentry\configureScope(function (Scope $scope) use ($request): void {
                $scope->setTag('request.route', (string) Route::currentRouteName());
                $scope->setTag('request.method', $request->method());
                $scope->setTag('request.url', $request->url());
            });

            Sentry\configureScope(function (Scope $scope): void {
                $requestId = Context::get('request_id');
                $traceId = Context::get('trace_id');

                $scope->setTag('perscom.request_id', $requestId);
                $scope->setTag('perscom.trace_id', $traceId);

                $scope->setContext('PERSCOM', [
                    'Request ID' => $requestId,
                    'Trace ID' => $traceId,
                ]);
            });

            if (Auth::guard($guard)->check()) {
                Sentry\configureScope(function (Scope $scope) use ($guard): void {
                    $scope->setUser([
                        'id' => Auth::guard($guard)->user()->getAuthIdentifier(),
                        'name' => Auth::guard($guard)->user()->name,
                        'email' => Auth::guard($guard)->user()->email,
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

            if ($request->header('X-Perscom-Sdk') === 'true') {
                Sentry\configureScope(function (Scope $scope) use ($request): void {
                    $scope->setTag('sdk', 'true');

                    $scope->setContext('SDK', [
                        'Name' => 'perscom-php-sdk',
                        'Version' => $request->header('X-Perscom-Sdk-Version'),
                    ]);
                });
            }
        }

        return $next($request);
    }
}
