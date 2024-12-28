<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);

        $response->headers->set('Surrogate-Control', 'max-age=2592000');
        $response->headers->set('Cache-Control', 'no-store, max-age=0, public');
        $response->headers->remove('Pragma');
        $response->headers->remove('Expires');
        $response->setVary('Authorization, X-Perscom-Id');

        return $response;
    }
}
