<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Middleware\InitializeTenancyByRequestData;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response;

/**
 * Executes requests against the REST API from inside the application, on behalf of the caller
 * of the current request. The caller's bearer token is forwarded, so the API's authentication,
 * scopes, policies, validation and serialization apply exactly as they do for external clients.
 */
class InternalApiService
{
    /**
     * @param  array<string, mixed>  $query
     */
    public static function get(string $path, array $query = []): Response
    {
        return static::send('GET', $path, $query);
    }

    /**
     * @param  array<string, mixed>  $body
     * @param  array<string, mixed>  $query
     */
    public static function post(string $path, array $body = [], array $query = []): Response
    {
        return static::send('POST', $path, $query, $body);
    }

    /**
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>  $body
     */
    public static function send(string $method, string $path, array $query = [], array $body = []): Response
    {
        $original = request();

        $uri = sprintf('%s/%s/%s', rtrim((string) config('api.url'), '/'), config('api.version'), ltrim($path, '/'));

        if (filled($query)) {
            $uri .= '?'.http_build_query($query);
        }

        $server = collect([
            'HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => $original->headers->get('Authorization'),
            'HTTP_'.str_replace('-', '_', strtoupper(InitializeTenancyByRequestData::$header)) => tenancy()->initialized ? (string) tenant()->getTenantKey() : null,
            'HTTP_USER_AGENT' => $original->userAgent(),
            'REMOTE_ADDR' => $original->ip(),
        ])->filter(fn (?string $value): bool => filled($value))->all();

        /**
         * Build the request the same way Request::capture() does so a JSON body is exposed
         * through the request bag, which is where Orion reads search parameters from.
         */
        $subRequest = Request::createFromBase(SymfonyRequest::create($uri, $method, [], [], [], $server, filled($body) ? (string) json_encode($body) : null));

        try {
            return resolve(Kernel::class)->handle($subRequest);
        } finally {
            resolve('app')->instance('request', $original);
            Facade::clearResolvedInstance('request');
        }
    }
}
