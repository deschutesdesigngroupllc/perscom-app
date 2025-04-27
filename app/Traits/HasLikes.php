<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\ModelLike;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @mixin Eloquent
 */
trait HasLikes
{
    /**
     * @return MorphToMany<User, $this>
     */
    public function likes(): MorphToMany
    {
        return $this->morphToMany(User::class, 'model', 'model_has_likes')
            ->orderByPivot('created_at')
            ->using(ModelLike::class)
            ->withTimestamps();
    }
}
