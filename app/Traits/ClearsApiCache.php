<?php

declare(strict_types=1);

namespace App\Traits;

use App\Jobs\PurgeApiCache;
use Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Eloquent
 */
trait ClearsApiCache
{
    protected static function bootClearsApiCache(): void
    {
        self::created(function (Model $model) {
            PurgeApiCache::dispatch(
                tag: $model,
                purgeAll: true
            );
        });

        self::updated(function (Model $model) {
            PurgeApiCache::dispatch($model);
        });

        self::deleted(function (Model $model) {
            PurgeApiCache::dispatch($model);
        });
    }
}
