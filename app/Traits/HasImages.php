<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Image;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @mixin Eloquent
 *
 * @template TModel of Model
 */
trait HasImages
{
    /**
     * @return MorphOne<Image, TModel>
     */
    public function image(): MorphOne
    {
        /** @var TModel $this */
        return $this->morphOne(Image::class, 'model')->latest();
    }

    /**
     * @return MorphMany<Image, TModel>
     */
    public function images(): MorphMany
    {
        /** @var TModel $this */
        return $this->morphMany(Image::class, 'model');
    }
}
