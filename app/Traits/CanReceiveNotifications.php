<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\ModelNotification;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;

/**
 * @mixin Eloquent
 */
trait CanReceiveNotifications
{
    public function modelNotifications(): MorphToMany
    {
        return $this->morphToMany(static::class, Str::of(class_basename(static::class))->singular()->lower()->toString(), 'model_has_notifications')
            ->withPivot(['event', 'message'])
            ->orderByPivot('created_at')
            ->using(ModelNotification::class)
            ->withTimestamps();
    }
}
