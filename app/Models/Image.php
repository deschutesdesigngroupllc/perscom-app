<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $appends = ['image_url'];

    /**
     * @return string|null
     */
    public function getImageUrlAttribute()
    {
        return $this->path ? Storage::url($this->path) : null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function model()
    {
        return $this->morphTo('model');
    }
}
