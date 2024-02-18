<?php

namespace App\Traits;

use Spatie\ResponseCache\Facades\ResponseCache;

trait ClearsResponseCache
{
    public static function bootClearsResponseCache(): void
    {
        if (config('responsecache.enabled')) {
            self::created(function () {
                ResponseCache::clear();
            });

            self::updated(function () {
                ResponseCache::clear();
            });

            self::deleted(function () {
                ResponseCache::clear();
            });
        }
    }
}
