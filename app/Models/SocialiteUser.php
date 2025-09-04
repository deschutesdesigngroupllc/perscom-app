<?php

declare(strict_types=1);

namespace App\Models;

use DutchCodingCompany\FilamentSocialite\Exceptions\ImplementationException;
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Models\SocialiteUser as BaseSocialiteUser;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;

/**
 * @property int $id
 * @property int $user_id
 * @property string $provider
 * @property string $provider_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static>|SocialiteUser newModelQuery()
 * @method static Builder<static>|SocialiteUser newQuery()
 * @method static Builder<static>|SocialiteUser query()
 * @method static Builder<static>|SocialiteUser whereCreatedAt($value)
 * @method static Builder<static>|SocialiteUser whereId($value)
 * @method static Builder<static>|SocialiteUser whereProvider($value)
 * @method static Builder<static>|SocialiteUser whereProviderId($value)
 * @method static Builder<static>|SocialiteUser whereUpdatedAt($value)
 * @method static Builder<static>|SocialiteUser whereUserId($value)
 *
 * @mixin \Eloquent
 */
class SocialiteUser extends BaseSocialiteUser
{
    /**
     * @throws ImplementationException
     */
    public static function findForProvider(string $provider, SocialiteUserContract $oauthUser): ?BaseSocialiteUser
    {
        /** @var class-string<Model&Authenticatable> $user */
        $user = FilamentSocialitePlugin::current()->getUserModelClass();

        $socialiteUser = null;
        if ($exists = $user::query()->where(static::getProviderColumn($provider), $oauthUser->getId())->first()) {
            $relationship = "{$provider}User";

            if (method_exists($exists, $relationship) && $exists->$relationship) {
                $socialiteUser = $exists->$relationship;
            }
        }

        if (blank($socialiteUser)) {
            $socialiteUser = parent::findForProvider($provider, $oauthUser);
        }

        if (filled($socialiteUser)) {
            static::updateUser($provider, $oauthUser, $socialiteUser);
        }

        return $socialiteUser;
    }

    /**
     * @throws ImplementationException
     */
    public static function createForProvider(string $provider, SocialiteUserContract $oauthUser, Authenticatable $user): BaseSocialiteUser
    {
        /** @var SocialiteUser $socialiteUser */
        $socialiteUser = parent::createForProvider($provider, $oauthUser, $user);

        static::updateUser($provider, $oauthUser, $socialiteUser);

        return $socialiteUser;
    }

    /**
     * @throws ImplementationException
     */
    protected static function getProviderColumn(string $provider): string
    {
        return match ($provider) {
            'discord' => 'discord_user_id',
            'facebook' => 'facebook_user_id',
            'github' => 'github_user_id',
            'google' => 'google_user_id',
            default => throw new ImplementationException('The provided provider does not exist.'),
        };
    }

    /**
     * @throws ImplementationException
     */
    protected static function updateUser(string $provider, SocialiteUserContract $oauthUser, SocialiteUser $socialiteUser): void
    {
        $column = static::getProviderColumn($provider);

        $socialiteUser->user->$column = $oauthUser->getId();
        $socialiteUser->user->save();
    }
}
