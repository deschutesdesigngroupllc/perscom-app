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

        $response->headers->set('Surrogate-Control', 'max-age=30, public, s-maxage=2592000');
        $response->setVary('Authorization, X-Perscom-Id');
        $response->setCache([
            'max_age' => 30,
            's_maxage' => 2592000,
        ]);

        return $response;
    }
}
