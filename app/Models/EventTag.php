<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $tag_id
 * @property int $event_id
 *
 * @method static Builder<static>|EventTag newModelQuery()
 * @method static Builder<static>|EventTag newQuery()
 * @method static Builder<static>|EventTag query()
 * @method static Builder<static>|EventTag whereEventId($value)
 * @method static Builder<static>|EventTag whereTagId($value)
 *
 * @mixin \Eloquent
 */
class EventTag extends Pivot
{
    protected $table = 'events_tags';

    protected $fillable = [
        'tag_id',
        'event_id',
    ];
}
