<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Rank extends Model implements Sortable
{
    use HasFactory;
    use SortableTrait;

    /**
     * @var string[]
     */
    protected $fillable = ['name', 'description', 'abbreviation', 'paygrade', 'image', 'order'];

    /**
     * @var string[]
     */
    protected $appends = ['image_url'];

    /**
     * @return string|null
     */
    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : null;
    }
}
