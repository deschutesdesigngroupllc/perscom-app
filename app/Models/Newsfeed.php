<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\NewsfeedScope;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasColorField;
use App\Traits\HasLikes;
use Database\Factories\NewsfeedFactory;
use Eloquent;
use Filament\Support\Contracts\HasColor;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * App\Models\Newsfeed
 *
 * @property int $id
 * @property string|null $log_name
 * @property array<array-key, mixed> $description
 * @property string|null $subject_type
 * @property string|null $event
 * @property int|null $subject_id
 * @property string|null $causer_type
 * @property string|null $causer_id
 * @property Collection<array-key, mixed>|null $properties
 * @property string|null $batch_uuid
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|null $causer
 * @property-read string|null $color
 * @property-read Collection $changes
 * @property-read string|null $headline
 * @property-read string|null $item
 * @property-read ModelLike|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $likes
 * @property-read int|null $likes_count
 * @property-read Model|null $subject
 * @property-read string|null $text
 *
 * @method static Builder<static>|Newsfeed causedBy(Model $causer)
 * @method static NewsfeedFactory factory($count = null, $state = [])
 * @method static Builder<static>|Newsfeed forBatch(string $batchUuid)
 * @method static Builder<static>|Newsfeed forEvent(string $event)
 * @method static Builder<static>|Newsfeed forSubject(Model $subject)
 * @method static Builder<static>|Newsfeed hasBatch()
 * @method static Builder<static>|Newsfeed inLog(...$logNames)
 * @method static Builder<static>|Newsfeed newModelQuery()
 * @method static Builder<static>|Newsfeed newQuery()
 * @method static Builder<static>|Newsfeed query()
 * @method static Builder<static>|Newsfeed whereBatchUuid($value)
 * @method static Builder<static>|Newsfeed whereCauserId($value)
 * @method static Builder<static>|Newsfeed whereCauserType($value)
 * @method static Builder<static>|Newsfeed whereCreatedAt($value)
 * @method static Builder<static>|Newsfeed whereDescription($value)
 * @method static Builder<static>|Newsfeed whereEvent($value)
 * @method static Builder<static>|Newsfeed whereId($value)
 * @method static Builder<static>|Newsfeed whereLogName($value)
 * @method static Builder<static>|Newsfeed whereProperties($value)
 * @method static Builder<static>|Newsfeed whereSubjectId($value)
 * @method static Builder<static>|Newsfeed whereSubjectType($value)
 * @method static Builder<static>|Newsfeed whereUpdatedAt($value)
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

    /**
     * @return Attribute<?string, never>
     */
    public function headline(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => optional($this->properties, fn (): mixed => $this->getExtraProperty('headline'))
        )->shouldCache();
    }

    /**
     * @return Attribute<?string, never>
     */
    public function text(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => optional($this->properties, fn (): mixed => $this->getExtraProperty('text'))
        )->shouldCache();
    }

    /**
     * @return Attribute<?string, never>
     */
    public function color(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => optional($this->properties, fn (): mixed => $this->getExtraProperty('color'))
        )->shouldCache();
    }

    /**
     * @return Attribute<?string, never>
     */
    public function item(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => optional($this->properties, fn (): mixed => $this->getExtraProperty('item'))
        )->shouldCache();
    }
}
