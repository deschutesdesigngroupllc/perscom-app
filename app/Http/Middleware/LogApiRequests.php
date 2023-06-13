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
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (tenant()) {
            $client = Auth::guard('passport')->client(); // @phpstan-ignore-line
            $properties['client'] = $client->id ?? null;

            $name = match (true) {
                Auth::guard('jwt')->check() => 'jwt',
                $client?->firstParty() => 'api',
                ! $client?->firstParty() => 'oauth',
                default => 'api'
            };

            $causer = match (true) {
                $client?->firstParty() => Auth::guard('api')->user(),
                ! $client?->firstParty() => $client,
                default => null
            };

            activity($name)->withProperties([
                'client' => $client->id ?? null,
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
            ])->causedBy($causer)->log($request->getPathInfo());
        }

        return $response;
    }
}
