<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $event_id
 * @property int $category_id
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Category $category
 * @property-read Event $event
 *
 * @method static Builder<static>|EventCategory newModelQuery()
 * @method static Builder<static>|EventCategory newQuery()
 * @method static Builder<static>|EventCategory query()
 * @method static Builder<static>|EventCategory whereCategoryId($value)
 * @method static Builder<static>|EventCategory whereCreatedAt($value)
 * @method static Builder<static>|EventCategory whereEventId($value)
 * @method static Builder<static>|EventCategory whereId($value)
 * @method static Builder<static>|EventCategory whereOrder($value)
 * @method static Builder<static>|EventCategory whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class EventCategory extends Pivot
{
    protected $table = 'events_categories';

    protected $fillable = [
        'event_id',
        'category_id',
        'order',
        'created_at',
        'updated_at',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
