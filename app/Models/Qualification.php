<?php

namespace App\Models;

use App\Models\Scopes\QualificationScope;
use App\Traits\HasImages;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * App\Models\Qualification
 *
 * @property-read \App\Models\Image|null $image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Image> $images
 * @property-read int|null $images_count
 *
 * @method static \Database\Factories\QualificationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Qualification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Qualification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Qualification ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Qualification query()
 *
 * @mixin \Eloquent
 */
class Qualification extends Model implements Sortable
{
    use HasFactory;
    use HasImages;
    use SortableTrait;

    protected static function booted(): void
    {
        static::addGlobalScope(new QualificationScope());
    }

    /**
     * @var string[]
     */
    protected $fillable = ['name', 'description', 'order'];

    /**
     * @var string[]
     */
    protected $with = ['image'];
}
