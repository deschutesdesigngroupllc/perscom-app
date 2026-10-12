<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A single dated instance of an event. One-time events have one occurrence and repeating
 * events have one per schedule date. Rows are created lazily, the first time an occurrence
 * needs to hold state such as a registration, attendance or a cancellation.
 *
 * @property int $id
 * @property int $event_id
 * @property \Illuminate\Support\Carbon $starts
 * @property \Illuminate\Support\Carbon|null $ends
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Event $event
 * @property-read bool $is_cancelled
 * @property-read \Illuminate\Database\Eloquent\Collection<int, EventRegistration> $registrations
 * @property-read int|null $registrations_count
 *
 * @method static Builder<static>|EventOccurrence active()
 * @method static Builder<static>|EventOccurrence cancelled()
 * @method static \Database\Factories\EventOccurrenceFactory factory($count = null, $state = [])
 * @method static Builder<static>|EventOccurrence newModelQuery()
 * @method static Builder<static>|EventOccurrence newQuery()
 * @method static Builder<static>|EventOccurrence query()
 * @method static Builder<static>|EventOccurrence whereCancelledAt($value)
 * @method static Builder<static>|EventOccurrence whereCreatedAt($value)
 * @method static Builder<static>|EventOccurrence whereEnds($value)
 * @method static Builder<static>|EventOccurrence whereEventId($value)
 * @method static Builder<static>|EventOccurrence whereId($value)
 * @method static Builder<static>|EventOccurrence whereNotes($value)
 * @method static Builder<static>|EventOccurrence whereStarts($value)
 * @method static Builder<static>|EventOccurrence whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class EventOccurrence extends Model
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasFactory;

    protected $table = 'events_occurrences';

    protected $fillable = [
        'event_id',
        'starts',
        'ends',
        'cancelled_at',
        'notes',
        'created_at',
        'updated_at',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Registrations made for this occurrence only. Series-wide registrations live on the event.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    protected function scopeCancelled(Builder $query): void
    {
        $query->whereNotNull('cancelled_at');
    }

    protected function scopeActive(Builder $query): void
    {
        $query->whereNull('cancelled_at');
    }

    /**
     * @return Attribute<bool, never>
     */
    protected function isCancelled(): Attribute
    {
        return Attribute::get(fn (): bool => filled($this->cancelled_at));
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts' => 'datetime',
            'ends' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }
}
