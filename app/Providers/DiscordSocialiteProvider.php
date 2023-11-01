<?php

namespace App\Providers;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;

class DiscordSocialiteProvider extends AbstractProvider
{
    /**
     * @var string[]
     */
    protected $scopes = ['email', 'identify'];

    protected $scopeSeparator = ' ';

    public function getDiscordUrl(): string
    {
        return config('services.discord.base_uri');
    }

    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase($this->getDiscordUrl().'/oauth2/authorize', $state);
    }

    protected function getTokenUrl(): string
    {
        return $this->getDiscordUrl().'/oauth2/token';
    }

    // @phpstan-ignore-next-line
    protected function getUserByToken($token): mixed
    {
        $response = $this->getHttpClient()->get($this->getDiscordUrl().'/users/@me', [
            'headers' => [
                'cache-control' => 'no-cache',
                'Authorization' => 'Bearer '.$token,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param  array<string, mixed>  $user
     */
    protected function mapUserToObject(array $user): User
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['id'],
            'name' => $user['username'],
            'email' => $user['email'] ?? null,
        ]);
    }
}
