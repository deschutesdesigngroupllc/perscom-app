<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\Image
 *
 * @property-read string|null $image_url
 * @property-read Model|\Eloquent $model
 *
 * @method static \Database\Factories\ImageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Image newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Image newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Image query()
 *
 * @mixin \Eloquent
 */
class Image extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): ?string
    {
        return $this->path ? Storage::url($this->path) : null;
    }

    public function model(): MorphTo
    {
        return $this->morphTo('model');
    }
}
