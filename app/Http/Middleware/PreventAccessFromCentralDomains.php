<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains as BasePreventAccessFromCentralDomains;

class PreventAccessFromCentralDomains extends BasePreventAccessFromCentralDomains
{
    public function handle(Request $request, Closure $next)
    {
        if (! config('tenancy.enabled')) {
            return $next($request);
        }

        return parent::handle($request, $next);
    }
}
