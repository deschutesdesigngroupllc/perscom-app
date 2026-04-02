<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class EnabledInEnvironment
{
    public function handle(Request $request, Closure $next, ...$environments): Response
    {
        if (! App::environment($environments)) {
            return back();
        }

        return $next($request);
    }
}
