<?php

namespace App\Models;

use App\Models\Scopes\QualificationScope;
use App\Traits\HasImages;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * App\Models\Qualification
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read \App\Models\Image|null $image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Image> $images
 * @property-read int|null $images_count
 *
 * @method static \Database\Factories\QualificationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Qualification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Qualification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Qualification ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Qualification query()
 * @method static \Illuminate\Database\Eloquent\Builder|Qualification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Qualification whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Qualification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Qualification whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Qualification whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Qualification whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Qualification extends Model implements Sortable
{
    use HasFactory;
    use HasImages;
    use SortableTrait;

    /**
     * @var string[]
     */
    protected $fillable = ['name', 'description', 'order', 'updated_at', 'created_at'];

    /**
     * @var string[]
     */
    protected $with = ['image'];

    protected static function booted(): void
    {
        static::addGlobalScope(new QualificationScope());
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'qualifications_categories')
            ->withPivot('order')
            ->withTimestamps();
    }
}
