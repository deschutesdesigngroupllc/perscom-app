<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Symfony\Component\HttpFoundation\Response;

class AttachTraceAndRequestId
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Perscom-Request-Id', Context::get('request_id'));
        $response->headers->set('X-Perscom-Trace-Id', Context::get('trace_id'));

        return $response;
    }
}
