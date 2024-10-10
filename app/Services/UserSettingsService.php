<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Settings\UserSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UserSettingsService
{
    private static string $cacheKey = 'user_settings';

    public static function save(string $key, mixed $value, ?User $user = null): bool
    {
        $user ??= Auth::user();

        if (! $user) {
            return false;
        }

        /** @var UserSettings $settings */
        $settings = app(UserSettings::class);
        $previous = $settings->$key;
        $settings->$key = data_set($previous, $user->getKey(), $value);
        $settings->save();

        UserSettingsService::flush();

        return true;
    }

    public static function get(string $key, mixed $default = null, bool $flush = false, ?User $user = null): mixed
    {
        $user ??= Auth::user();

        if (! $user) {
            return null;
        }

        $cacheKey = UserSettingsService::$cacheKey."_{$user->getKey()}";

        if ($flush) {
            Cache::forget($cacheKey);
        }

        /** @var array<string, mixed> $settings * */
        $settings = Cache::remember($cacheKey, now()->addHour(), function () use ($user) {
            /** @var UserSettings $settings */
            $settings = app(UserSettings::class);

            return collect($settings->toArray())->map(function ($values) use ($user) {
                return $values[$user->getKey()] ?? null;
            })->filter()->toArray();
        });

        return data_get($settings, $key, $default);
    }

    public static function flush(?User $user = null): ?bool
    {
        $user ??= Auth::user();

        if (! $user) {
            return null;
        }

        $cacheKey = UserSettingsService::$cacheKey."_{$user->getKey()}";

        return Cache::forget($cacheKey);
    }
}
