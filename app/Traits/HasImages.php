<?php

namespace App\Traits;

use App\Models\Image;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasImages
{
    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'model');
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'model');
    }
}
