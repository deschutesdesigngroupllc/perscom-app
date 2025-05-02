<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MoveApiKeyQueryParameterToHeader
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (blank($request->bearerToken()) && filled($token = $request->query('apikey'))) {
            $request->headers->set('Authorization', 'Bearer '.$token);
        }

        return $next($request);
    }
}
