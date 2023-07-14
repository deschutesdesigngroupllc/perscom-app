<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ForceJsonResponse
{
    public function handle(Request $request, Closure $next): Response|RedirectResponse|JsonResponse
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
