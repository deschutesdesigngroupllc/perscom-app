<?php

declare(strict_types=1);

namespace App\Services;

use App\Settings\UserSettings;
use Illuminate\Support\Facades\Auth;

class UserSettingsService
{
    public static function save(string $key, mixed $value): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        /** @var UserSettings $settings */
        $settings = app(UserSettings::class);
        $previous = $settings->$key;
        $settings->$key = data_set($previous, $user->getKey(), $value);
        $settings->save();

        return true;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $user = Auth::user();

        if (! $user) {
            return null;
        }

        /** @var UserSettings $settings */
        $settings = app(UserSettings::class);

        return data_get($settings->$key, $user->getKey(), $default);
    }
}
