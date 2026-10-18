<?php

declare(strict_types=1);

namespace App\Mcp\Tools\Concerns;

use Illuminate\Support\Arr;
use Laravel\Mcp\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

trait RespondsWithApiResults
{
    /**
     * Convert a REST API response into an MCP response, turning API errors into
     * messages an AI client can act on.
     */
    protected function respondWithApiResult(HttpResponse $response, ?string $scope = null): Response
    {
        $status = $response->getStatusCode();

        /** @var array<string, mixed>|null $payload */
        $payload = json_decode((string) $response->getContent(), true);

        if ($status >= 200 && $status < 300) {
            return Response::json($payload ?? []);
        }

        return Response::error(match ($status) {
            HttpResponse::HTTP_UNAUTHORIZED => 'PERSCOM rejected the access token. Reconnect the PERSCOM integration to sign in again.',
            HttpResponse::HTTP_PAYMENT_REQUIRED => 'This organization does not have an active PERSCOM subscription.',
            HttpResponse::HTTP_FORBIDDEN => filled($scope)
                ? sprintf('You do not have permission to view this. The connection needs the "%s" scope and your account must be allowed to access it.', $scope)
                : 'You do not have permission to view this.',
            HttpResponse::HTTP_NOT_FOUND => 'The requested record could not be found.',
            HttpResponse::HTTP_UNPROCESSABLE_ENTITY => trim('The request was invalid. '.collect(Arr::flatten((array) data_get($payload, 'error.errors', [])))->join(' ')),
            HttpResponse::HTTP_TOO_MANY_REQUESTS => 'Too many requests were made. Wait a minute before trying again.',
            default => trim(sprintf('PERSCOM returned an unexpected error (HTTP %d). %s', $status, data_get($payload, 'error.message', ''))),
        });
    }
}
