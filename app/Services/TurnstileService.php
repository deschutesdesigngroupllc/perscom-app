<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class TurnstileService
{
    protected static string $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    /**
     * @throws ConnectionException
     */
    public static function validate(string $turnstileResponse): bool
    {
        $response = Http::asJson()
            ->timeout(30)
            ->connectTimeout(10)
            ->post(static::$url, [
                'secret' => config('services.cloudflare.turnstile.secret_key'),
                'response' => $turnstileResponse,
            ]);

        return (bool) $response->json('success');
    }
}
