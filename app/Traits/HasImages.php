<?php

namespace App\Traits;

use App\Models\Image;

trait HasImages
{
    /**
     * @return mixed
     */
    public function image()
    {
        return $this->morphOne(Image::class, 'model');
    }

    /**
     * @return mixed
     */
    public function images()
    {
        return $this->morphMany(Image::class, 'model');
    }
}
