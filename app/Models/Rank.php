<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\EloquentSortable\SortableTrait;

class Rank extends Model
{
    use HasFactory;
    use SortableTrait;

    /**
     * @var string[]
     */
    protected $appends = [
        'image_url',
    ];

    /**
     * @return string|null
     */
    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : null;
    }
}
