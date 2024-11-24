<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\User;
use App\Services\UserSettingsService;
use App\Settings\OrganizationSettings;
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
        $timezone = UserSettingsService::get('timezone', function () {
            /** @var OrganizationSettings $settings */
            $settings = app(OrganizationSettings::class);

            return $settings->timezone ?? config('app.timezone');
        });

        return [
            'name' => $this->name,
            'preferred_username' => $this->email,
            'profile' => $this->url,
            'email' => $this->email,
            'email_verified' => $this->hasVerifiedEmail(),
            'picture' => $this->profile_photo_url,
            'phone_number' => $this->phone_number,
            'tenant' => (string) tenant()->getTenantKey(),
            'roles' => $this->roles->pluck('name')->toArray(),
            'locale' => config('app.locale'),
            'zoneinfo' => $timezone,
            'updated_at' => Carbon::parse($this->updated_at)->getTimestamp(),
        ];
    }
}
