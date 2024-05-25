<?php

namespace App\Models;

use App\Traits\ClearsResponseCache;
use App\Traits\HasTags;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Calendar
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 *
 * @method static \Database\Factories\CalendarFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar query()
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar tags(?mixed $tag)
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Calendar extends Model
{
    use ClearsResponseCache;
    use HasFactory;
    use HasTags;
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $fillable = ['name', 'description', 'color', 'updated_at', 'created_at'];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
