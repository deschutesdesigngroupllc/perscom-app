<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Spatie\LaravelSettings\Settings;

class SettingsService
{
    public static function all(string $class): mixed
    {
        return SettingsService::withCache($class);
    }

    public static function get(string $class, string $key, mixed $default = null, bool $flush = false): mixed
    {
        if ($flush) {
            SettingsService::flush($class);
        }

        $settings = SettingsService::withCache($class);

        return data_get($settings, $key, $default);
    }

    public static function flush(string $class): ?bool
    {
        return Cache::forget(SettingsService::cacheKey($class));
    }

    protected static function cacheKey(string $class): string
    {
        return class_basename($class);
    }

    protected static function withCache(string $class): mixed
    {
        return Cache::remember(SettingsService::cacheKey($class), now()->addHour(), function () use ($class) {
            /** @var Settings $settings */
            $settings = app($class);

            return $settings->toArray();
        });
    }
}
