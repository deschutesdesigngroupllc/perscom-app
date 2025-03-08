<?php

declare(strict_types=1);

namespace App\Traits;

use Eloquent;
use Spatie\ResponseCache\Facades\ResponseCache;

/**
 * @mixin Eloquent
 */
trait ClearsResponseCache
{
    protected static function bootClearsResponseCache(): void
    {
        if (config('responsecache.enabled')) {
            self::created(function (): void {
                ResponseCache::clear();
            });

            self::updated(function (): void {
                ResponseCache::clear();
            });

            self::deleted(function (): void {
                ResponseCache::clear();
            });
        }
    }
}
