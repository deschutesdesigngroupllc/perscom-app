<?php

namespace App\Models;

use App\Traits\HasAttachments;
use App\Traits\HasAuthor;
use App\Traits\HasImages;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use RRule\RRule;

class Event extends Model
{
    use HasAuthor;
    use HasAttachments;
    use HasFactory;
    use HasImages;

    /**
     * @var string[]
     */
    protected $casts = [
        'all_day' => 'boolean',
        'start' => 'datetime',
        'end' => 'datetime',
        'repeats' => 'boolean',
        'by_day' => 'array',
        'by_month' => 'array',
        'by_month_day' => 'array',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'recurrence_rule',
        'recurrence_rule_string',
    ];

    /**
     * Boot
     */
    public static function boot()
    {
        parent::boot();

        static::saved(function (Event $event) {
            if ($event->repeats) {
            }
        });
    }

    /**
     * @return \Illuminate\Support\Optional|mixed
     */
    public function getRecurrenceRuleAttribute()
    {
        return optional($this->repeats, function () {
            $payload = [
                'DTSTART' => $this->start,
                'FREQ' => $this->frequency,
                'INTERVAL' => $this->interval,
            ];

            if ($this->end) {
                $payload['UNTIL'] = $this->end;
            }

            if ($this->by_day) {
                $payload['BYDAY'] = $this->by_day;
                if ($this->by_day_position) {
                    $payload['BYDAY'] = $this->by_day_position.$this->by_day;
                }
            }

            if ($this->by_month) {
                $payload['BYMONTH'] = $this->by_month;
            }

            if ($this->by_month_day) {
                $payload['BYMONTHDAY'] = $this->by_month_day;
            }

            try {
                return new RRule($payload);
            } catch (\Exception $exception) {
                Log::error($exception->getMessage());

                return null;
            }
        });
    }

    /**
     * @return \Illuminate\Support\Optional|mixed
     */
    public function getRecurrenceRuleStringAttribute()
    {
        return optional($this->recurrence_rule, function ($rule) {
            return $rule->humanReadable();
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function calendar()
    {
        return $this->belongsTo(Calendar::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'events_tags');
    }
}
