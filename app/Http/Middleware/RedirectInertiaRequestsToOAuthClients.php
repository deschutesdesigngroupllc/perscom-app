<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class RedirectInertiaRequestsToOAuthClients
{
    /**
     * Approving or denying an authorization request redirects to the client's callback, usually on
     * another origin. Inertia submits these forms over XHR, which cannot follow a cross-origin
     * redirect, so hand the URL to Inertia to perform a full page visit instead. Passport converts
     * its PSR response into a plain Symfony response, so check the status and Location header
     * rather than for a RedirectResponse.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $location = $response->headers->get('Location');

        if ($request->inertia() && $response->isRedirect() && filled($location)) {
            return Inertia::location($location);
        }

        return $response;
    }
}
