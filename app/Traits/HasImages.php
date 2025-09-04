<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Image;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @mixin Eloquent
 */
trait HasImages
{
    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'model')->latest();
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'model');
    }
}
