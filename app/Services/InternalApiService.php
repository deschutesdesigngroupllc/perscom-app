<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Middleware\InitializeTenancyByRequestData;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Str;
use JsonException;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response;

use function sprintf;

class InternalApiService
{
    /**
     * Send a GET request to the API as the current caller.
     *
     * @param  array<string, mixed>  $query
     *
     * @throws JsonException when the request body cannot be encoded
     */
    public static function get(string $path, array $query = []): Response
    {
        return static::send('GET', $path, $query);
    }

    /**
     * Send a POST request to the API as the current caller.
     *
     * @param  array<string, mixed>  $body
     * @param  array<string, mixed>  $query
     *
     * @throws JsonException when the request body cannot be encoded
     */
    public static function post(string $path, array $body = [], array $query = []): Response
    {
        return static::send('POST', $path, $query, $body);
    }

    /**
     * Send a request to the API as the current caller.
     *
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>  $body
     *
     * @throws JsonException when the request body cannot be encoded
     */
    public static function send(string $method, string $path, array $query = [], array $body = []): Response
    {
        $original = request();

        $uri = sprintf('%s/%s/%s', mb_rtrim((string) config('api.url'), '/'), config('api.version'), mb_ltrim($path, '/'));

        if (filled($query)) {
            $uri .= '?'.http_build_query($query);
        }

        $tenantHeader = 'HTTP_'.Str::replace('-', '_', Str::upper(InitializeTenancyByRequestData::$header));

        $server = collect([
            'HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => $original->headers->get('Authorization'),
            $tenantHeader => tenancy()->initialized ? (string) tenant()->getTenantKey() : null,
            'HTTP_USER_AGENT' => $original->userAgent(),
            'REMOTE_ADDR' => $original->ip(),
        ])->filter(fn (?string $value): bool => filled($value))->all();

        $content = filled($body) ? json_encode($body, JSON_THROW_ON_ERROR) : null;

        /**
         * Build the request the same way Request::capture() does so a JSON body is exposed
         * through the request bag, which is where Orion reads search parameters from.
         */
        $subRequest = Request::createFromBase(SymfonyRequest::create($uri, $method, [], [], [], $server, $content));

        try {
            return resolve(Kernel::class)->handle($subRequest);
        } finally {
            resolve('app')->instance('request', $original);
            Facade::clearResolvedInstance('request');
        }
    }
}
