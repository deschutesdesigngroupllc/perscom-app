<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetAuthGuard
{
    public function handle(Request $request, Closure $next, string $guard): Response
    {
        Auth::shouldUse($guard);

        return $next($request);
    }
}
