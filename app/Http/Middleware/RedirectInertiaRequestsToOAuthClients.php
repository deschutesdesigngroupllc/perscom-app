<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class RedirectInertiaRequestsToOAuthClients
{
    /**
     * Approving or denying an authorization request redirects to the client's callback, usually on
     * another origin. Inertia submits these forms over XHR, which cannot follow a cross-origin
     * redirect, so hand the URL to Inertia to perform a full page visit instead.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->header('X-Inertia') && $response instanceof RedirectResponse) {
            return Inertia::location($response->getTargetUrl());
        }

        return $response;
    }
}
