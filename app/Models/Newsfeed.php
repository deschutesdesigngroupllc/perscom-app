<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\NewsfeedScope;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasColorField;
use App\Traits\HasLikes;
use Eloquent;
use Filament\Support\Contracts\HasColor;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Newsfeed
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
 * @property-read \Illuminate\Database\Eloquent\Model|Eloquent|null $causer
 * @property-read string|null $color
 * @property-read \Illuminate\Support\Collection $changes
 * @property-read string|null $headline
 * @property-read string|null $item
 * @property-read ModelLike $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $likes
 * @property-read int|null $likes_count
 * @property-read \Illuminate\Database\Eloquent\Model|Eloquent|null $subject
 * @property-read string|null $text
 *
 * @method static Builder|Newsfeed causedBy(\Illuminate\Database\Eloquent\Model $causer)
 * @method static \Database\Factories\NewsfeedFactory factory($count = null, $state = [])
 * @method static Builder|Newsfeed forBatch(string $batchUuid)
 * @method static Builder|Newsfeed forEvent(string $event)
 * @method static Builder|Newsfeed forSubject(\Illuminate\Database\Eloquent\Model $subject)
 * @method static Builder|Newsfeed hasBatch()
 * @method static Builder|Newsfeed inLog(...$logNames)
 * @method static \Illuminate\Database\Eloquent\Builder|Newsfeed newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Newsfeed newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Newsfeed query()
 * @method static \Illuminate\Database\Eloquent\Builder|Newsfeed whereBatchUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Newsfeed whereCauserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Newsfeed whereCauserType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Newsfeed whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Newsfeed whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Newsfeed whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Newsfeed whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Newsfeed whereLogName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Newsfeed whereProperties($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Newsfeed whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Newsfeed whereSubjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Newsfeed whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
#[ScopedBy(NewsfeedScope::class)]
class Newsfeed extends Activity implements HasColor
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasColorField;
    use HasFactory;
    use HasLikes;

    public $guarded = [
        'description',
    ];

    protected $appends = [
        'headline',
        'text',
        'item',
    ];

    public function headline(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => optional($this->properties, function () {
                return $this->getExtraProperty('headline');
            })
        )->shouldCache();
    }

    public function text(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => optional($this->properties, function () {
                return $this->getExtraProperty('text');
            })
        )->shouldCache();
    }

    public function color(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => optional($this->properties, function () {
                return $this->getExtraProperty('color');
            })
        )->shouldCache();
    }

    public function item(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => optional($this->properties, function () {
                return $this->getExtraProperty('item');
            })
        )->shouldCache();
    }
}
