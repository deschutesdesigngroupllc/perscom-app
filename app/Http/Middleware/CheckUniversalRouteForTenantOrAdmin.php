<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedOnDomainException;
use Symfony\Component\HttpFoundation\Response;

class CheckUniversalRouteForTenantOrAdmin
{
    /**
     * @throws \Throwable
     */
    public function handle(Request $request, Closure $next): Response
    {
        throw_if(! tenant() && ! $request->is([
            'admin',
            'admin/*',
            'nova-api/*',
        ]), TenantCouldNotBeIdentifiedOnDomainException::class, $request->getHost());

        return $next($request);
    }
}
