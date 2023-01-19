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

    /**
     * @var string
     */
    protected $scopeSeparator = ' ';

    /**
     * @return string
     */
    public function getDiscordUrl()
    {
        return config('services.discord.base_uri');
    }

    /**
     * @param  string  $state
     * @return string
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase($this->getDiscordUrl().'/oauth2/authorize', $state);
    }

    /**
     * @return string
     */
    protected function getTokenUrl()
    {
        return $this->getDiscordUrl().'/oauth2/token';
    }

    /**
     * @param  string  $token
     * @return array|mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getUserByToken($token)
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
     * @param  array  $user
     * @return User
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['id'],
            'nickname' => sprintf('%s#%s', $user['username'], $user['discriminator']),
            'name' => $user['username'],
            'email' => $user['email'] ?? null,
            'avatar' => $this->formatAvatar($user),
        ]);
    }

    /**
     * @param  array  $user
     * @return string|null
     *
     * @see https://discord.com/developers/docs/reference#image-formatting-cdn-endpoints
     */
    protected function formatAvatar(array $user)
    {
        if (empty($user['avatar'])) {
            return null;
        }

        $isGif = preg_match('/a_.+/m', $user['avatar']) === 1;
        $extension = $this->getConfig('allow_gif_avatars', true) && $isGif ? 'gif'
            : $this->getConfig('avatar_default_extension', 'jpg');

        return sprintf('https://cdn.discordapp.com/avatars/%s/%s.%s', $user['id'], $user['avatar'], $extension);
    }
}
