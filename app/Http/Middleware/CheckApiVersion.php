<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiVersion
{
    public function handle(Request $request, Closure $next): Response
    {
        $version = request()->segment(1);

        abort_unless(in_array($version, config('api.versions', [])), 400, 'The provided API version is not valid. The currently available versions are: '.collect(config('api.versions'))->join(', '));

        return $next($request);
    }
}
