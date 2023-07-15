<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasLikes
{
    public function likes(): MorphToMany
    {
        return $this->morphToMany(User::class, 'model', 'model_has_likes')
            ->orderByPivot('created_at')
            ->withTimestamps();
    }
}
