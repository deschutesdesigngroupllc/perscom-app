<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Activity;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LogApiResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response instanceof StreamedResponse) {
            return $response;
        }

        $logId = $request->attributes->get('activity_id');

        if (blank($logId)) {
            return $response;
        }

        $log = Activity::find($logId);

        if (blank($log)) {
            return $response;
        }

        /** @var Collection<string, mixed> $properties */
        $properties = collect($log->properties);

        $log->forceFill([
            'properties' => $properties
                ->put('status', $response->getStatusCode())
                ->put('response_headers', iterator_to_array($response->headers->getIterator()))
                ->put('content', optional($response->getContent(), static function ($content) {
                    if (Str::isJson($content)) {
                        return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
                    }

                    if (! mb_check_encoding($content, 'UTF-8')) {
                        return null;
                    }

                    return $content;
                })),
        ])->save();

        return $response;
    }
}
