<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\ModelNotification;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @mixin Eloquent
 */
trait HasNotifications
{
    public static function bootHasNotifications(): void
    {
        static::deleting(fn ($model) => $model->modelNotifications()->delete());
    }

    public function modelNotifications(): MorphMany
    {
        return $this->morphMany(ModelNotification::class, 'model');
    }
}
