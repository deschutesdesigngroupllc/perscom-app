<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRegistrationEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs('register') && ! setting('registration_enabled', true)) {
            abort(403, setting('registration_disabled_message', 'Registration is disabled.'));
        }

        return $next($request);
    }
}
