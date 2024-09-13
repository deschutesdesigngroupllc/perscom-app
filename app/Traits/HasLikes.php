<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Like;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @mixin Eloquent
 */
trait HasLikes
{
    public function likes(): MorphToMany
    {
        return $this->morphToMany(User::class, 'model', 'model_has_likes')
            ->orderByPivot('created_at')
            ->using(Like::class)
            ->withTimestamps();
    }
}
