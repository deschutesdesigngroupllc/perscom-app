<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (tenant()) {
            $client = Auth::guard('passport')->client(); // @phpstan-ignore-line

            $name = match (true) {
                Auth::guard('jwt')->check() => 'jwt',
                optional($client)->firstParty() => 'api',
                ! optional($client)->firstParty() => 'oauth',
                default => 'api'
            };

            $causer = match (true) {
                optional($client)->firstParty() => Auth::guard('api')->user(),
                ! optional($client)->firstParty() => $client,
                default => null
            };

            activity($name)->withProperties([
                'client' => $client->id ?? null,
                'endpoint' => $request->getPathInfo(),
                'method' => $request->getMethod(),
                'status' => $response->getStatusCode(),
                'ip' => $request->getClientIp(),
                'request_headers' => iterator_to_array($request->headers->getIterator()),
                'response_headers' => iterator_to_array($response->headers->getIterator()),
                'body' => optional($request->getContent(), static function ($content) {
                    if (Str::isJson($content)) {
                        return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
                    }

                    return $content;
                }),
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
