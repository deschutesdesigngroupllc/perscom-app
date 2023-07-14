<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Sentry;
use Sentry\State\Scope;

class SentryContext
{
    public function handle(Request $request, Closure $next): Response|RedirectResponse|JsonResponse
    {
        if (app()->bound('sentry')) {
            if (Auth::check()) {
                Sentry\configureScope(function (Scope $scope): void {
                    $scope->setUser([
                        'id' => Auth::user()->getAuthIdentifier(),
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
