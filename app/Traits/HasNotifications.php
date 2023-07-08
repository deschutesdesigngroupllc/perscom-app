<?php

namespace App\Traits;

use App\Models\User;

trait HasNotifications
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function notifications()
    {
        return $this->morphToMany(User::class, 'model', 'model_has_notifications')
            ->orderByPivot('created_at')
            ->withTimestamps();
    }
}
