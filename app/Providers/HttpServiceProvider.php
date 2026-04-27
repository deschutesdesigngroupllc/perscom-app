<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use Psr\Http\Message\RequestInterface;

class HttpServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $userAgent = sprintf(
            '%s/%s (+%s)',
            str_replace(' ', '-', (string) config('app.name', 'PERSCOM')),
            config('app.version', '1.0'),
            config('app.url'),
        );

        Http::globalRequestMiddleware(fn (RequestInterface $request): RequestInterface => $request->withHeader('User-Agent', $userAgent));
    }
}
