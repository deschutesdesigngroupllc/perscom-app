<?php

use Illuminate\Support\Facades\Cache;

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null, int $ttl = 120, bool $force = false): mixed
    {
        if ($force) {
            return nova_get_setting($key, $default);
        }

        return Cache::remember($key, $ttl, static function () use ($key, $default) {
            try {
                return nova_get_setting($key, $default);
            } catch (Exception $exception) {
                return $default;
            }
        });
    }
}
