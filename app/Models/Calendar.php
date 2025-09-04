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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $color
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Event> $events
 * @property-read int|null $events_count
 * @property-read string $label
 * @property-read string|null $relative_url
 * @property-read Collection<int, Tag> $tags
 * @property-read int|null $tags_count
 * @property-read string|null $url
 *
 * @method static \Database\Factories\CalendarFactory factory($count = null, $state = [])
 * @method static Builder<static>|Calendar newModelQuery()
 * @method static Builder<static>|Calendar newQuery()
 * @method static Builder<static>|Calendar query()
 * @method static Builder<static>|Calendar whereColor($value)
 * @method static Builder<static>|Calendar whereCreatedAt($value)
 * @method static Builder<static>|Calendar whereDescription($value)
 * @method static Builder<static>|Calendar whereId($value)
 * @method static Builder<static>|Calendar whereName($value)
 * @method static Builder<static>|Calendar whereUpdatedAt($value)
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

    /**
     * @return HasMany<Event, $this>
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
