<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $tag_id
 * @property int $calendar_id
 *
 * @method static \Illuminate\Database\Eloquent\Builder|CalendarTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CalendarTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CalendarTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|CalendarTag whereCalendarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CalendarTag whereTagId($value)
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
