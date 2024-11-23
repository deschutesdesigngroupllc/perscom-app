<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Activity;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Passport\Guards\TokenGuard;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! tenancy()->initialized) {
            return $response;
        }

        /** @var TokenGuard $passport */
        $passport = Auth::guard('passport');
        $client = $passport->client();

        $name = match (true) {
            optional($client)->firstParty() => 'api',
            ! optional($client)->firstParty() => 'oauth',
            default => 'api'
        };

        $causer = match (true) {
            optional($client)->firstParty() => Auth::guard('api')->user(),
            ! optional($client)->firstParty() => $client,
            default => null
        };

        /** @var Activity $activity */
        $activity = activity($name)->withProperties([
            'client' => $client->id ?? null,
            'endpoint' => $request->getPathInfo(),
            'method' => $request->getMethod(),
            'ip' => $request->getClientIp(),
            'request_headers' => iterator_to_array($request->headers->getIterator()),
            'body' => optional($request->getContent(), static function ($content) {
                if (Str::isJson($content)) {
                    return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
                }

                if (! mb_check_encoding($content, 'UTF-8')) {
                    return null;
                }

                return $content;
            }),
        ])->causedBy($causer)->log($request->getPathInfo());

        $request->attributes->add([
            'activity_id' => $activity->getKey(),
        ]);

        return $response;
    }
}
