<?php

namespace App\Models;

use App\Models\Scopes\RankScope;
use App\Traits\ClearsResponseCache;
use App\Traits\HasImages;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * App\Models\Rank
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $abbreviation
 * @property string|null $paygrade
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read \App\Models\Image|null $image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Image> $images
 * @property-read int|null $images_count
 *
 * @method static \Database\Factories\RankFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Rank newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rank newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rank onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Rank ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Rank query()
 * @method static \Illuminate\Database\Eloquent\Builder|Rank whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rank whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rank whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rank whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rank whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rank whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rank whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rank wherePaygrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rank whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rank withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Rank withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Rank extends Model implements Sortable
{
    use ClearsResponseCache;
    use HasFactory;
    use HasImages;
    use SoftDeletes;
    use SortableTrait;

    /**
     * @var array<int, string>
     */
    protected $fillable = ['name', 'description', 'abbreviation', 'paygrade', 'order', 'updated_at', 'created_at'];

    /**
     * @var string[]
     */
    protected $with = ['image'];

    protected static function booted(): void
    {
        static::addGlobalScope(new RankScope());
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'ranks_categories')
            ->withPivot('order')
            ->withTimestamps();
    }
}
