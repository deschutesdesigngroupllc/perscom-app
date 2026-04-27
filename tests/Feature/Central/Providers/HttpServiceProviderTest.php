<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

it('attaches a User-Agent header to outgoing HTTP client requests', function (): void {
    Http::fake([
        '*' => Http::response(['ok' => true], 200),
    ]);

    Http::get('https://example.com/ping');

    Http::assertSent(function (Request $request): bool {
        $userAgent = $request->header('User-Agent')[0] ?? null;

        expect($userAgent)
            ->not->toBeNull()
            ->toContain((string) config('app.url'))
            ->toMatch('#/.+ \(\+#');

        return true;
    });
});
