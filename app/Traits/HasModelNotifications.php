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
            if (filled($model)) {
                SendModelNotifications::dispatch($model, 'created');
            }
        });

        static::updated(function ($model) {
            if (filled($model)) {
                SendModelNotifications::dispatch($model, 'updated');
            }
        });

        static::deleted(function ($model) {
            if (filled($model)) {
                SendModelNotifications::dispatch($model, 'deleted');
            }
        });

        static::deleting(fn ($model) => $model->modelNotifications()->delete());
    }

    /**
     * @return MorphMany<ModelNotification, $this>
     */
    public function modelNotifications(): MorphMany
    {
        return $this->morphMany(ModelNotification::class, 'model');
    }
}
