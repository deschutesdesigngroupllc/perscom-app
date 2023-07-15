<?php

namespace App\Models;

use App\Models\Scopes\NewsfeedScope;
use App\Traits\HasLikes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\NewsfeedItem
 *
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $causer
 * @property-read \Illuminate\Support\Collection $changes
 * @property-read string|null $color
 * @property-read string|null $headline
 * @property-read string|null $item
 * @property-read string|null $text
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $likes
 * @property-read int|null $likes_count
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $subject
 *
 * @method static Builder|Activity causedBy(\Illuminate\Database\Eloquent\Model $causer)
 * @method static \Database\Factories\NewsfeedItemFactory factory($count = null, $state = [])
 * @method static Builder|Activity forBatch(string $batchUuid)
 * @method static Builder|Activity forEvent(string $event)
 * @method static Builder|Activity forSubject(\Illuminate\Database\Eloquent\Model $subject)
 * @method static Builder|Activity hasBatch()
 * @method static Builder|Activity inLog(...$logNames)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsfeedItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewsfeedItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewsfeedItem query()
 *
 * @mixin \Eloquent
 */
class NewsfeedItem extends Activity
{
    use HasFactory;
    use HasLikes;

    /**
     * @var string[]
     */
    protected $appends = ['headline', 'text', 'color', 'item'];

    protected static function booted(): void
    {
        static::addGlobalScope(new NewsfeedScope());
    }

    public function getHeadlineAttribute(): ?string
    {
        return optional($this->properties, function () {
            return $this->getExtraProperty('headline');
        });
    }

    public function getTextAttribute(): ?string
    {
        return optional($this->properties, function () {
            return $this->getExtraProperty('text');
        });
    }

    public function getColorAttribute(): ?string
    {
        return optional($this->properties, function () {
            return $this->getExtraProperty('color');
        });
    }

    public function getItemAttribute(): ?string
    {
        return optional($this->properties, function () {
            return $this->getExtraProperty('item');
        });
    }
}
