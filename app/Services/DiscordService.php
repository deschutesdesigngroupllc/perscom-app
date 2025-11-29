<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\DiscordRateLimitException;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Sleep;
use Spatie\Url\Url;

class DiscordService
{
    public static function addBotToServerLink(): string
    {
        return Url::fromString('https://discord.com/api/oauth2/authorize')
            ->withQueryParameters([
                'client_id' => config('services.discord.client_id'),
                'scope' => 'bot',
                'permissions' => 470027443,
            ])
            ->__toString();
    }

    /**
     * @throws ConnectionException
     * @throws DiscordRateLimitException
     */
    public static function getGuilds(): mixed
    {
        return with(new self, fn (DiscordService $service): mixed => $service->withRateLimitHandler(function (DiscordService $service) {
            $response = $service
                ->client()
                ->get('users/@me/guilds');

            $data = $response->json();

            if ($response->getStatusCode() === 429) {
                throw DiscordRateLimitException::withData($data);
            }

            if (! $response->successful()) {
                Log::debug('Discord get guilds error', $data);

                return null;
            }

            return $data;
        }));
    }

    /**
     * @throws ConnectionException
     * @throws DiscordRateLimitException
     */
    public static function getChannels(string|int $guildId): mixed
    {
        return with(new self, fn (DiscordService $service): mixed => $service->withRateLimitHandler(function (DiscordService $service) use ($guildId) {
            $response = $service
                ->client()
                ->get(sprintf('guilds/%s/channels', $guildId));

            $data = $response->json();

            if ($response->getStatusCode() === 429) {
                throw DiscordRateLimitException::withData($data);
            }

            if (! $response->successful()) {
                Log::debug('Discord get channels error', $data);

                return null;
            }

            return $data;
        }));
    }

    public function client(): PendingRequest
    {
        return Http::baseUrl('https://discord.com/api/')
            ->withToken(config('services.discord.token'), 'Bot')
            ->withUserAgent('PERSCOM')
            ->asJson()
            ->acceptJson();
    }

    private function withRateLimitHandler(Closure $callback, int $maxRetries = 5): mixed
    {
        $retryCount = 0;
        while ($retryCount < $maxRetries) {
            try {
                return value($callback, $this);
            } catch (DiscordRateLimitException $exception) {
                $retryCount++;

                $retryAfter = data_get($exception->getData(), 'retry_after', 10);

                Sleep::until(Carbon::now()->addMilliseconds($retryAfter));
            }
        }

        return null;
    }
}
