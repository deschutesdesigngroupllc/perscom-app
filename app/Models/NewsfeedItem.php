<?php

namespace App\Models;

use App\Models\Scopes\NewsfeedScope;
use App\Traits\HasLikes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\NewsfeedItem
 *
 * @property int $id
 * @property string|null $log_name
 * @property array $description
 * @property string|null $subject_type
 * @property string|null $event
 * @property int|null $subject_id
 * @property string|null $causer_type
 * @property string|null $causer_id
 * @property \Illuminate\Support\Collection|null $properties
 * @property string|null $batch_uuid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $causer
 * @property-read \Illuminate\Support\Collection $changes
 * @property-read mixed|null $color
 * @property-read mixed|null $headline
 * @property-read mixed|null $item
 * @property-read mixed|null $text
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
 * @method static \Illuminate\Database\Eloquent\Builder|NewsfeedItem whereBatchUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsfeedItem whereCauserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsfeedItem whereCauserType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsfeedItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsfeedItem whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsfeedItem whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsfeedItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsfeedItem whereLogName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsfeedItem whereProperties($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsfeedItem whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsfeedItem whereSubjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsfeedItem whereUpdatedAt($value)
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

    public function getHeadlineAttribute(): mixed
    {
        return optional($this->properties, function () {
            return $this->getExtraProperty('headline');
        });
    }

    public function getTextAttribute(): mixed
    {
        return optional($this->properties, function () {
            return $this->getExtraProperty('text');
        });
    }

    public function getColorAttribute(): mixed
    {
        return optional($this->properties, function () {
            return $this->getExtraProperty('color');
        });
    }

    public function getItemAttribute(): mixed
    {
        return optional($this->properties, function () {
            return $this->getExtraProperty('item');
        });
    }
}
