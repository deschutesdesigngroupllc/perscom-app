<?php

namespace App\Traits;

use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @mixin Eloquent
 */
trait HasNotifications
{
    public function notifications(): MorphToMany
    {
        return $this->morphToMany(User::class, 'model', 'model_has_notifications')
            ->orderByPivot('created_at')
            ->withTimestamps();
    }
}
