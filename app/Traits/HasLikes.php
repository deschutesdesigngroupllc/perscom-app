<?php

namespace App\Traits;

use App\Models\User;

trait HasLikes
{
    /**
     * @return mixed
     */
    public function likes()
    {
        return $this->morphToMany(User::class, 'model', 'model_has_likes')
            ->orderByPivot('created_at')
            ->withTimestamps();
    }
}
