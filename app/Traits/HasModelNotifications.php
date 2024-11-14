<?php

declare(strict_types=1);

namespace App\Traits;

use App\Jobs\SendModelNotifications;
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
        static::created(function ($model) {
            SendModelNotifications::dispatch($model, 'created');
        });

        static::updated(function ($model) {
            SendModelNotifications::dispatch($model, 'updated');
        });

        static::deleting(fn ($model) => $model->modelNotifications()->delete());
    }

    public function modelNotifications(): MorphMany
    {
        return $this->morphMany(ModelNotification::class, 'model');
    }
}
