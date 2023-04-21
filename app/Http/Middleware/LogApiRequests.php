<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LogApiRequests
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (tenant()) {
            $activity = activity('api')->withProperties([
                'endpoint' => $request->getPathInfo(),
                'method' => $request->getMethod(),
                'status' => $response->getStatusCode(),
                'ip' => $request->getClientIp(),
                'request_headers' => (string) $request->headers,
                'response_headers' => (string) $response->headers,
                'content' => optional($response->getContent(), static function ($content) {
                    if (Str::isJson($content)) {
                        return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
                    }

                    return $content;
                }),
            ]);

            if (Auth::guard('api')->check()) {
                $activity->causedBy(Auth::guard('api')->user()->getAuthIdentifier());
            } else {
                $activity->causedByAnonymous();
            }

            $activity->log($request->getPathInfo());
        }

        return $response;
    }
}
