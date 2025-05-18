<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\SocialiteUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\HigherOrderTapProxy;

/**
 * @mixin User
 */
trait SocialRelationships
{
    /**
     * @return HasOne<SocialiteUser, $this>
     */
    public function discordUser(): HasOne
    {
        return $this->hasOne(SocialiteUser::class)
            ->where('provider', 'discord');
    }

    /**
     * @return Attribute<bool, never>
     */
    public function discordConnected(): Attribute
    {
        return Attribute::get(fn (): bool => filled($this->discord_user_id))
            ->shouldCache();
    }

    public function disconnectDiscordAccount(): User|HigherOrderTapProxy
    {
        return tap($this, function (User $user): void {
            $user->discordUser()->delete();
            $user->update([
                'discord_user_id' => null,
                'discord_private_channel_id' => null,
            ]);
        });
    }

    /**
     * @return HasOne<SocialiteUser, $this>
     */
    public function facebookUser(): HasOne
    {
        return $this->hasOne(SocialiteUser::class)
            ->where('provider', 'facebook');
    }

    /**
     * @return Attribute<bool, never>
     */
    public function facebookConnected(): Attribute
    {
        return Attribute::get(fn (): bool => filled($this->facebook_user_id))
            ->shouldCache();
    }

    public function disconnectFacebookAccount(): User|HigherOrderTapProxy
    {
        return tap($this, function (User $user): void {
            $user->facebookUser()->delete();
            $user->update([
                'facebook_user_id' => null,
            ]);
        });
    }

    /**
     * @return HasOne<SocialiteUser, $this>
     */
    public function githubUser(): HasOne
    {
        return $this->hasOne(SocialiteUser::class)
            ->where('provider', 'github');
    }

    /**
     * @return Attribute<bool, never>
     */
    public function githubConnected(): Attribute
    {
        return Attribute::get(fn (): bool => filled($this->github_user_id))
            ->shouldCache();
    }

    public function disconnectGithubAccount(): User|HigherOrderTapProxy
    {
        return tap($this, function (User $user): void {
            $user->githubUser()->delete();
            $user->update([
                'github_user_id' => null,
            ]);
        });
    }

    /**
     * @return HasOne<SocialiteUser, $this>
     */
    public function googleUser(): HasOne
    {
        return $this->hasOne(SocialiteUser::class)
            ->where('provider', 'google');
    }

    /**
     * @return Attribute<bool, never>
     */
    public function googleConnected(): Attribute
    {
        return Attribute::get(fn (): bool => filled($this->google_user_id))
            ->shouldCache();
    }

    public function disconnectGoogleAccount(): User|HigherOrderTapProxy
    {
        return tap($this, function (User $user): void {
            $user->googleUser()->delete();
            $user->update([
                'google_user_id' => null,
            ]);
        });
    }
}
