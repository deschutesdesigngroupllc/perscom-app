<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Comment;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @mixin Eloquent
 *
 * @template TModel of Model
 */
trait HasComments
{
    /**
     * @return MorphMany<Comment, TModel>
     */
    public function comments(): MorphMany
    {
        /** @var TModel $this */
        return $this->morphMany(Comment::class, 'model');
    }
}
