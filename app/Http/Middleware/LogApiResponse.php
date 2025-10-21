<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\ApiLog;
use App\Services\JsonService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LogApiResponse
{
    public function __construct(
        private readonly JsonService $jsonService,
    ) {
        //
    }

    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if ($response instanceof StreamedResponse) {
            return $response;
        }

        $logId = $request->attributes->get('activity_id');

        if (blank($logId)) {
            return $response;
        }

        $log = ApiLog::find($logId);

        if (blank($log)) {
            return $response;
        }

        $processed = $this->jsonService->processForLogging($response->getContent());

        /** @var Collection<string, mixed> $properties */
        $properties = collect($log->properties);

        $log->forceFill([
            'properties' => $properties
                ->put('response_headers', iterator_to_array($response->headers->getIterator()))
                ->put('content', $processed['content'] ?? null)
                ->put('is_truncated', $processed['is_truncated'] ?? false),
        ])->save();

        $log->setMeta([
            'status' => $response->getStatusCode(),
            'duration' => (string) round((microtime(true) - LARAVEL_START) * 1000),
        ]);

        return $response;
    }
}
