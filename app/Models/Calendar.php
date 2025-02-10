<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\CalendarObserver;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasColorField;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use App\Traits\HasTags;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Event> $events
 * @property-read int|null $events_count
 * @property-read string $label
 * @property-read string|null $relative_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Tag> $tags
 * @property-read int|null $tags_count
 * @property-read string|null $url
 *
 * @method static \Database\Factories\CalendarFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
#[ObservedBy(CalendarObserver::class)]
class Calendar extends Model implements HasColor, HasLabel
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasColorField;
    use HasFactory;
    use HasResourceLabel;
    use HasResourceUrl;
    use HasTags;

    protected $fillable = [
        'name',
        'description',
        'created_at',
        'updated_at',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
