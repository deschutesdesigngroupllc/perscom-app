<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedOnDomainException;

class CheckUniversalRouteForTenantOrAdmin
{
    /**
     * @throws \Throwable
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse|JsonResponse
    {
        throw_if(! tenant() && ! $request->is([
            'admin',
            'admin/*',
            'nova-api/*',
        ]), TenantCouldNotBeIdentifiedOnDomainException::class, $request->getHost());

        return $next($request);
    }
}
