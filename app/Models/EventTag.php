<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
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
 * @mixin \Illuminate\Database\Eloquent\Model
 */
#[Fillable([
    'tag_id',
    'event_id',
])]
#[Table(name: 'events_tags')]
class EventTag extends Pivot {}
