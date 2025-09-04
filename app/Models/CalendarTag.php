<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $tag_id
 * @property int $calendar_id
 *
 * @method static Builder<static>|CalendarTag newModelQuery()
 * @method static Builder<static>|CalendarTag newQuery()
 * @method static Builder<static>|CalendarTag query()
 * @method static Builder<static>|CalendarTag whereCalendarId($value)
 * @method static Builder<static>|CalendarTag whereTagId($value)
 *
 * @mixin \Eloquent
 */
class CalendarTag extends Pivot
{
    protected $table = 'calendars_tags';

    protected $fillable = [
        'tag_id',
        'calendar_id',
    ];
}
