<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasNotifications
{
    public function notifications(): MorphToMany
    {
        return $this->morphToMany(User::class, 'model', 'model_has_notifications')
            ->orderByPivot('created_at')
            ->withTimestamps();
    }
}
