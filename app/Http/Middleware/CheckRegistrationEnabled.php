<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CheckRegistrationEnabled
{
    public function handle(Request $request, Closure $next): \Illuminate\Http\Response|RedirectResponse|JsonResponse
    {
        if ($request->routeIs('register') && ! setting('registration_enabled', true)) {
            abort(403, setting('registration_disabled_message', 'Registration is disabled.'));
        }

        return $next($request);
    }
}
