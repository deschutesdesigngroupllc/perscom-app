<?php

declare(strict_types=1);

namespace App\Services;

use App\Settings\IntegrationSettings;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Spatie\Url\Url;
use Throwable;

class SlackService
{
    /**
     * Post to public channels without being invited, post to channels the bot joins, and list
     * channels to choose from.
     *
     * @var string[]
     */
    public const array SCOPES = ['chat:write', 'chat:write.public', 'channels:read'];

    public static function isConfigured(): bool
    {
        return filled(config('services.slack.client_id')) && filled(config('services.slack.client_secret'));
    }

    public static function installUrl(string $state): string
    {
        return Url::fromString('https://slack.com/oauth/v2/authorize')
            ->withQueryParameters([
                'client_id' => config('services.slack.client_id'),
                'scope' => collect(self::SCOPES)->implode(','),
                'redirect_uri' => self::installRedirectUri(),
                'state' => $state,
            ])
            ->__toString();
    }

    public static function installRedirectUri(): string
    {
        return config('services.slack.install_redirect') ?: route('integrations.install.callback', ['provider' => 'slack']);
    }

    /**
     * Exchange the OAuth install code for the workspace's bot token.
     *
     * @return array{team_id: string, team_name: string, bot_token: string}|null
     *
     * @throws ConnectionException
     */
    public static function exchangeInstallCode(string $code): ?array
    {
        $response = Http::asForm()
            ->acceptJson()
            ->post('https://slack.com/api/oauth.v2.access', [
                'client_id' => config('services.slack.client_id'),
                'client_secret' => config('services.slack.client_secret'),
                'code' => $code,
                'redirect_uri' => self::installRedirectUri(),
            ]);

        if (! $response->successful() || $response->json('ok') !== true || blank($response->json('access_token'))) {
            Log::warning('Slack install code exchange failed', [
                'status' => $response->status(),
                'error' => $response->json('error'),
                'redirect_uri' => self::installRedirectUri(),
            ]);

            return null;
        }

        return [
            'team_id' => (string) $response->json('team.id'),
            'team_name' => (string) $response->json('team.name'),
            'bot_token' => (string) $response->json('access_token'),
        ];
    }

    /**
     * Get the workspace's public channels keyed by ID.
     *
     * @return array<string, string>
     *
     * @throws ConnectionException
     */
    public static function channels(): array
    {
        $token = self::token();

        if (blank($token)) {
            return [];
        }

        $response = self::client($token)->get('conversations.list', [
            'types' => 'public_channel',
            'exclude_archived' => true,
            'limit' => 500,
        ]);

        if ($response->json('ok') !== true) {
            Log::warning('Slack channel list failed', ['error' => $response->json('error')]);

            return [];
        }

        return collect((array) $response->json('channels'))
            ->mapWithKeys(fn (array $channel): array => [(string) data_get($channel, 'id') => '#'.data_get($channel, 'name')])
            ->sort()
            ->all();
    }

    /**
     * Post the message to the configured Slack channel.
     *
     * @param  array<string, mixed>  $message
     *
     * @throws ConnectionException
     */
    public static function postMessage(array $message): bool
    {
        $token = self::token();
        $channel = data_get(resolve(IntegrationSettings::class)->slack_settings, 'slack_channel');

        if (blank($token) || blank($channel)) {
            return false;
        }

        $response = self::client($token)->post('chat.postMessage', [...$message, 'channel' => $channel]);

        if ($response->json('ok') !== true) {
            Log::warning('Slack post message failed', ['error' => $response->json('error')]);

            return false;
        }

        return true;
    }

    /**
     * Revoke the stored bot token with Slack.
     *
     * @throws ConnectionException
     */
    public static function revoke(): void
    {
        $token = self::token();

        if (filled($token)) {
            self::client($token)->get('auth.revoke');
        }
    }

    /**
     * Bot tokens are stored encrypted in the organization's integration settings.
     */
    public static function token(): ?string
    {
        $encrypted = data_get(resolve(IntegrationSettings::class)->slack_settings, 'bot_token');

        if (blank($encrypted)) {
            return null;
        }

        try {
            return Crypt::decryptString((string) $encrypted);
        } catch (Throwable) {
            return null;
        }
    }

    protected static function client(string $token): PendingRequest
    {
        return Http::baseUrl('https://slack.com/api/')
            ->withToken($token)
            ->withUserAgent('PERSCOM')
            ->acceptJson()
            ->asJson();
    }
}
