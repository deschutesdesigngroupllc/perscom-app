<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Stancl\Tenancy\Features\UniversalRoutes;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain as BaseInitializeTenancyBySubdomain;
use Stancl\Tenancy\Resolvers\DomainTenantResolver;
use Stancl\Tenancy\Tenancy;

class InitializeTenancyBySubdomain extends BaseInitializeTenancyBySubdomain
{
    public function __construct(Tenancy $tenancy, DomainTenantResolver $resolver)
    {
        self::$onFail = static function ($exception, $request, $next) {
            if (UniversalRoutes::routeHasMiddleware($request->route(), UniversalRoutes::$middlewareGroup)) {
                return $next($request);
            }

            abort(404);
        };

        parent::__construct($tenancy, $resolver);
    }
}
