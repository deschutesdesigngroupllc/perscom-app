<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\DiscordRateLimitException;
use Closure;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;
use Spatie\Url\Url;

class DiscordService
{
    /**
     * The original notification permissions plus Embed Links (rich embeds) and Use Application Commands (slash commands).
     */
    public const string BOT_PERMISSIONS = '2617527475';

    /**
     * Connecting needs the OAuth client for the install and the bot token for everything after it, so
     * Discord is only offered when all three are set.
     */
    public static function isConfigured(): bool
    {
        return filled(config('services.discord.client_id'))
            && filled(config('services.discord.client_secret'))
            && filled(config('services.discord.token'));
    }

    public static function addBotToServerLink(): string
    {
        return Url::fromString('https://discord.com/api/oauth2/authorize')
            ->withQueryParameters([
                'client_id' => config('services.discord.client_id'),
                'scope' => 'bot applications.commands',
                'permissions' => DiscordService::BOT_PERMISSIONS,
            ])
            ->__toString();
    }

    /**
     * The OAuth flow that adds the bot to a server and tells us which server it was, so a server
     * can only be connected by someone who can manage it.
     */
    public static function installUrl(string $state): string
    {
        return Url::fromString('https://discord.com/oauth2/authorize')
            ->withQueryParameters([
                'client_id' => config('services.discord.client_id'),
                'scope' => 'bot applications.commands',
                'permissions' => DiscordService::BOT_PERMISSIONS,
                'response_type' => 'code',
                'redirect_uri' => DiscordService::installRedirectUri(),
                'state' => $state,
            ])
            ->__toString();
    }

    public static function installRedirectUri(): string
    {
        return config('services.discord.install_redirect') ?: route('integrations.install.callback', ['provider' => 'discord']);
    }

    /**
     * Exchanges the install code and returns the server the bot was added to.
     *
     * @return array{id: string, name: string}|null
     *
     * @throws ConnectionException
     */
    public static function exchangeInstallCode(string $code): ?array
    {
        $response = Http::asForm()
            ->acceptJson()
            ->post('https://discord.com/api/oauth2/token', [
                'client_id' => config('services.discord.client_id'),
                'client_secret' => config('services.discord.client_secret'),
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => DiscordService::installRedirectUri(),
            ]);

        if (! $response->successful() || blank($response->json('guild.id'))) {
            Log::warning('Discord install code exchange failed', [
                'status' => $response->status(),
                'error' => $response->json('error'),
                'error_description' => $response->json('error_description'),
                'redirect_uri' => DiscordService::installRedirectUri(),
            ]);

            return null;
        }

        return [
            'id' => (string) $response->json('guild.id'),
            'name' => (string) $response->json('guild.name'),
        ];
    }

    /**
     * Register the application's slash commands with Discord.
     *
     * @param  array<int, array<string, mixed>>  $commands
     *
     * @throws ConnectionException
     */
    public static function registerCommands(array $commands): bool
    {
        return DiscordService::send('put', sprintf('applications/%s/commands', config('services.discord.client_id')), $commands)?->successful() ?? false;
    }

    /**
     * Get the roles in the server.
     *
     * @return array<int, array<string, mixed>>
     *
     * @throws ConnectionException
     */
    public static function getRoles(string $guildId): array
    {
        return (array) DiscordService::send('get', sprintf('guilds/%s/roles', $guildId))?->json();
    }

    /**
     * Create a role in the server and return its id.
     *
     * @throws ConnectionException
     */
    public static function createRole(string $guildId, string $name): ?string
    {
        $response = DiscordService::send('post', sprintf('guilds/%s/roles', $guildId), [
            'name' => Str::limit($name, 97),
            'mentionable' => true,
        ]);

        return $response?->successful() ? (string) $response->json('id') : null;
    }

    /**
     * Get a member of the server.
     *
     * @return array<string, mixed>|null
     *
     * @throws ConnectionException
     */
    public static function getMember(string $guildId, string $userId): ?array
    {
        $response = DiscordService::send('get', sprintf('guilds/%s/members/%s', $guildId, $userId));

        return $response?->successful() ? (array) $response->json() : null;
    }

    /**
     * Add a role to a member of the server.
     *
     * @throws ConnectionException
     */
    public static function addMemberRole(string $guildId, string $userId, string $roleId): bool
    {
        return DiscordService::send('put', sprintf('guilds/%s/members/%s/roles/%s', $guildId, $userId, $roleId))?->successful() ?? false;
    }

    /**
     * Remove a role from a member of the server.
     *
     * @throws ConnectionException
     */
    public static function removeMemberRole(string $guildId, string $userId, string $roleId): bool
    {
        return DiscordService::send('delete', sprintf('guilds/%s/members/%s/roles/%s', $guildId, $userId, $roleId))?->successful() ?? false;
    }

    /**
     * Discord refuses to rename the server owner or anyone above the bot's highest role.
     *
     * @throws ConnectionException
     */
    public static function setNickname(string $guildId, string $userId, string $nickname): bool
    {
        return DiscordService::send('patch', sprintf('guilds/%s/members/%s', $guildId, $userId), [
            'nick' => Str::limit($nickname, 29),
        ])?->successful() ?? false;
    }

    /**
     * Remove the bot from the server.
     *
     * @throws ConnectionException
     */
    public static function leaveServer(string $guildId): bool
    {
        return DiscordService::send('delete', sprintf('users/@me/guilds/%s', $guildId))?->successful() ?? false;
    }

    /**
     * Get the servers the bot belongs to.
     *
     * @throws ConnectionException
     * @throws DiscordRateLimitException
     */
    public static function getGuilds(): mixed
    {
        $request = function (DiscordService $service): mixed {
            $response = $service
                ->client()
                ->get('users/@me/guilds');

            $data = $response->json();

            if ($response->getStatusCode() === 429) {
                throw DiscordRateLimitException::withData($data);
            }

            if (! $response->successful()) {
                Log::warning('Discord get guilds error', ['status' => $response->status(), 'body' => $data]);

                return null;
            }

            return $data;
        };

        return with(new self, fn (DiscordService $service): mixed => $service->withRateLimitHandler($request));
    }

    /**
     * Get the channels in the server.
     *
     * @throws ConnectionException
     * @throws DiscordRateLimitException
     */
    public static function getChannels(string|int $guildId): mixed
    {
        $request = function (DiscordService $service) use ($guildId): mixed {
            $response = $service
                ->client()
                ->get(sprintf('guilds/%s/channels', $guildId));

            $data = $response->json();

            if ($response->getStatusCode() === 429) {
                throw DiscordRateLimitException::withData($data);
            }

            if (! $response->successful()) {
                Log::warning('Discord get channels error', ['guild' => $guildId, 'status' => $response->status(), 'body' => $data]);

                return null;
            }

            return $data;
        };

        return with(new self, fn (DiscordService $service): mixed => $service->withRateLimitHandler($request));
    }

    /**
     * Discord reports retry_after in seconds (as a float). Web requests wait at most two seconds per
     * attempt so a rate limit never stalls a page; queued work honors the full delay up to a minute.
     *
     * @param  array<string, mixed>|null  $data
     */
    public static function retryDelayInMilliseconds(?array $data): int
    {
        $milliseconds = (int) ceil((float) data_get($data, 'retry_after', 1) * 1000);
        $maximum = App::runningInConsole() ? 60_000 : 2_000;

        return max(0, min($milliseconds, $maximum));
    }

    public function client(): PendingRequest
    {
        return Http::baseUrl('https://discord.com/api/')
            ->withToken(config('services.discord.token'), 'Bot')
            ->withUserAgent('PERSCOM')
            ->asJson()
            ->acceptJson();
    }

    /**
     * Send a request to the Discord API.
     *
     * @param  array<int|string, mixed>  $data
     *
     * @throws ConnectionException
     */
    protected static function send(string $method, string $uri, array $data = []): ?Response
    {
        return with(new self, fn (DiscordService $service): ?Response => $service->withRateLimitHandler(function (DiscordService $service) use ($method, $uri, $data): Response {
            $response = $service->client()->{$method}($uri, $data);

            if ($response->status() === 429) {
                throw DiscordRateLimitException::withData((array) $response->json());
            }

            if (! $response->successful()) {
                Log::warning('Discord API error', ['uri' => $uri, 'status' => $response->status(), 'body' => $response->json()]);
            }

            return $response;
        }));
    }

    private function withRateLimitHandler(Closure $callback, int $maxRetries = 5): mixed
    {
        $retryCount = 0;
        while ($retryCount < $maxRetries) {
            try {
                return value($callback, $this);
            } catch (DiscordRateLimitException $exception) {
                $retryCount++;

                Sleep::until(Date::now()->addMilliseconds(DiscordService::retryDelayInMilliseconds($exception->getData())));
            }
        }

        return null;
    }
}
