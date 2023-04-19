<?php

namespace App\Models;

use App\Models\Scopes\AwardScope;
use App\Traits\HasImages;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * App\Models\Award
 *
 * @property-read \App\Models\Image|null $image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Image> $images
 * @property-read int|null $images_count
 *
 * @method static \Database\Factories\AwardFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Award newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Award newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Award ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Award query()
 *
 * @mixin \Eloquent
 */
class Award extends Model implements Sortable
{
    use HasFactory;
    use HasImages;
    use SortableTrait;

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new AwardScope());
    }

    /**
     * @var string[]
     */
    protected $fillable = ['name', 'description'];

    /**
     * @var string[]
     */
    protected $with = ['image'];
}
