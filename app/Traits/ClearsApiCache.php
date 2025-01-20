<?php

declare(strict_types=1);

namespace App\Traits;

use App\Jobs\PurgeApiCache;
use App\Services\ApiCacheService;
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
                tags: collect([ApiCacheService::tagForModel($model), ApiCacheService::tagForModel($model, stripKey: true)]),
                event: 'created',
            );
        });

        self::updated(function (Model $model) {
            PurgeApiCache::dispatch(
                tags: ApiCacheService::tagForModel($model),
                event: 'updated'
            );
        });

        self::deleted(function (Model $model) {
            PurgeApiCache::dispatch(
                tags: ApiCacheService::tagForModel($model),
                event: 'deleted'
            );
        });
    }
}
