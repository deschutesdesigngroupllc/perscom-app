<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Comment;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @mixin Eloquent
 */
trait HasComments
{
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'model');
    }
}
