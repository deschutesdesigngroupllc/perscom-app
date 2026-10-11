<?php

declare(strict_types=1);

namespace App\Support\Passport;

use App\Models\User;
use Illuminate\Support\Arr;

class OidcClaims
{
    /**
     * The user's OpenID Connect claims, limited to those the granted scopes allow.
     *
     * @param  array<int, string>  $scopes
     * @return array<string, mixed>
     */
    public static function forScopes(User $user, array $scopes): array
    {
        $claims = collect($all = $user->getJWTCustomClaims());

        if (! in_array('email', $scopes, true)) {
            $claims->forget(['email', 'email_verified']);
        }

        if (! in_array('profile', $scopes, true)) {
            $claims->forget(array_keys(Arr::except($all, ['email', 'email_verified'])));
        }

        return $claims->toArray();
    }
}
