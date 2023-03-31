<?php

namespace App\Models;

use App\Traits\HasAttachments;
use App\Traits\HasAuthor;
use App\Traits\HasImages;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpRecurring\Enums\FrequencyEndTypeEnum;
use PhpRecurring\Enums\FrequencyTypeEnum;
use PhpRecurring\RecurringBuilder;
use PhpRecurring\RecurringConfig;

class Event extends Model
{
    use HasAuthor;
    use HasAttachments;
    use HasFactory;
    use HasImages;

    protected $fillable = [
        'name',
        'calendar_id',
        'description',
        'content',
        'location',
        'url',
        'author_id',
        'all_day',
        'start',
        'end',
        'repeats',
        'frequency',
        'interval',
        'end_type',
        'count',
        'until',
        'repeat_day',
        'repeat_month_day',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'all_day' => 'boolean',
        'start' => 'datetime',
        'end' => 'datetime',
        'repeats' => 'boolean',
        'interval' => 'integer',
        'count' => 'integer',
        'until' => 'datetime',
        'repeat_day' => 'array',
        'repeat_month_day' => 'integer',
    ];

    /**
     * Boot
     */
    public static function boot()
    {
        parent::boot();

        static::saved(function (Event $event) {
            if ($event->repeats) {
                if ($event->frequency === FrequencyTypeEnum::WEEK->value) {
                    $event->updateQuietly([
                        'repeat_month_day' => null,
                    ]);
                }

                if ($event->frequency === FrequencyTypeEnum::MONTH->value) {
                    $event->updateQuietly([
                        'repeat_day' => null,
                    ]);
                }

                if ($event->frequency === FrequencyTypeEnum::YEAR->value) {
                    $event->updateQuietly([
                        'repeat_day' => null,
                        'repeat_month_day' => null,
                    ]);
                }

                if ($event->end_type === FrequencyEndTypeEnum::NEVER->value) {
                    $event->updateQuietly([
                        'count' => null,
                        'until' => null,
                    ]);
                }
            }
        });
    }

    /**
     * @param  Builder  $query
     * @param $start
     * @param $end
     * @return Builder
     */
    public function scopeForDatePeriod(Builder $query, $start, $end)
    {
        $period = CarbonPeriod::create(
            Carbon::parse($start),
            Carbon::parse($end)
        );

        return $query->where(function (Builder $query) use ($period) {
            $query->whereBetween('start', [$period->getStartDate(), $period->getEndDate()]);
        })->orWhere(function (Builder $query) use ($period) {
            $query->whereBetween('end', [$period->getStartDate(), $period->getEndDate()]);
        })->orWhere(function (Builder $query) use ($period) {
            $query->whereDate('start', '<=', $period->getStartDate())->whereDate(
                'end',
                '>=',
                $period->getEndDate()
            );
        });
    }

    /**
     * @return \Illuminate\Support\Optional|mixed
     */
    public function getRecurrenceAttribute()
    {
        return optional($this->repeats, function () {
            $config = new RecurringConfig();
            $config->setStartDate($this->start)
                   ->setFrequencyType(FrequencyTypeEnum::from($this->frequency))
                   ->setFrequencyInterval($this->interval);

            if ($this->frequency === FrequencyTypeEnum::WEEK->value && $this->repeat_day) {
                $config->setRepeatIn($this->repeat_day);
            }

            if ($this->frequency === FrequencyTypeEnum::MONTH->value && $this->repeat_month_day) {
                $config->setRepeatIn($this->repeat_month_day);
            }

            if ($this->end_type) {
                $config->setFrequencyEndType(FrequencyEndTypeEnum::from($this->end_type));
            }

            if ($this->end_type === FrequencyEndTypeEnum::AFTER->value && $this->count) {
                $config->setFrequencyEndValue($this->count);
            }

            if ($this->end_type === FrequencyEndTypeEnum::IN->value && $this->until) {
                $config->setFrequencyEndValue($this->until);
            }

            return RecurringBuilder::forConfig($config)->startRecurring();
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
