<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sentry;
use Sentry\State\Scope;

class SentryContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (app()->bound('sentry')) {
            if (Auth::check()) {
                Sentry\configureScope(function (Scope $scope): void {
                    $scope->setUser([
                        'id' => Auth::user()->getAuthIdentifier(),
                        'username' => Auth::user()->name,
                        'email' => Auth::user()->email
                    ]);
                });
            }

            if (tenant()) {
                Sentry\configureScope(function (Scope $scope): void {
                    $scope->setTag('tenant', tenant()->getTenantKey());
                    $scope->setContext('tenant', [
                        'ID' => tenant()->getTenantKey(),
                        'Name' => tenant('name'),
                        'Email' => tenant('email')
                    ]);
                });
            }

            if ($request->routeIs('api.*')) {
                Sentry\configureScope(function (Scope $scope): void {
                    $scope->setTag('api', true);
                });
            }
        }

        return $next($request);
    }
}
