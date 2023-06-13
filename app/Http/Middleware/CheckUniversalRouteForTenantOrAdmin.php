<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedOnDomainException;

class CheckUniversalRouteForTenantOrAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        throw_if(! tenant() && ! $request->is([
            'admin',
            'admin/*',
            'nova-api/*',
        ]), TenantCouldNotBeIdentifiedOnDomainException::class, $request->getHost());

        return $next($request);
    }
}
