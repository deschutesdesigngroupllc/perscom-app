<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Carbon;

/**
 * @mixin User
 */
trait JwtClaims
{
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'name' => $this->name,
            'preferred_username' => $this->email,
            'profile' => $this->url,
            'email' => $this->email,
            'email_verified' => $this->hasVerifiedEmail(),
            'picture' => $this->profile_photo_url,
            'tenant' => (string) tenant()->getTenantKey(),
            'locale' => config('app.locale'),
            'zoneinfo' => config('app.timezone'),
            'updated_at' => Carbon::parse($this->updated_at)->getTimestamp(),
        ];
    }
}
