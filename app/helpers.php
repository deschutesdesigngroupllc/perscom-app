<?php

use Illuminate\Support\Facades\Cache;

if (! function_exists('setting')) {
    function setting($key, $default = null, int $ttl = 120)
    {
        return Cache::remember($key, $ttl, static function () use ($key, $default) {
            try {
                return nova_get_setting($key, $default);
            } catch (Exception $exception) {
                return $default;
            }
        });
    }
}
