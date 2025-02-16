<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\ModelLike;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @mixin Eloquent
 *
 * @template TModel of Model
 */
trait HasLikes
{
    /**
     * @return MorphToMany<User, TModel>
     */
    public function likes(): MorphToMany
    {
        /** @var TModel $this */
        return $this->morphToMany(User::class, 'model', 'model_has_likes')
            ->orderByPivot('created_at')
            ->using(ModelLike::class)
            ->withTimestamps();
    }
}
