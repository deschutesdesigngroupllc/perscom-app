<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogApiRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $activity = activity('api')->withProperties([
            'endpoint' => $request->getPathInfo(),
            'method' => $request->getMethod(),
            'status' => $response->getStatusCode(),
            'ip' => $request->getClientIp(),
            'request_headers' => (string) $request->headers,
            'response_headers' => (string) $response->headers,
        ]);

        if (Auth::guard('api')->check()) {
            $activity->causedBy(Auth::guard('api')->user());
        } else {
            $activity->causedByAnonymous();
        }

        //$activity->log($response->getContent());

        return $response;
    }
}
