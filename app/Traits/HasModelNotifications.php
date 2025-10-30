<?php

declare(strict_types=1);

namespace App\Traits;

use App\Jobs\Tenant\SendModelNotifications;
use App\Models\ModelNotification;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @mixin Eloquent
 */
trait HasModelNotifications
{
    public static function bootHasModelNotifications(): void
    {
        static::created(function ($model): void {
            if (filled($model)) {
                SendModelNotifications::dispatch($model, 'created');
            }
        });

        static::updated(function ($model): void {
            if (filled($model)) {
                SendModelNotifications::dispatch($model, 'updated');
            }
        });

        static::deleted(function ($model): void {
            if (filled($model)) {
                SendModelNotifications::dispatch($model, 'deleted');
            }
        });

        static::deleting(fn ($model) => $model->modelNotifications()->delete());
    }

    public function modelNotifications(): MorphMany
    {
        /** @phpstan-ignore return.type */
        return $this->morphMany(ModelNotification::class, 'model');
    }
}
