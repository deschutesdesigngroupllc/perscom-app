<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Enums\ScheduleEndType;
use App\Models\Enums\ScheduleFrequency;
use App\Models\Event;
use App\Models\Schedule;
use BackedEnum;
use Carbon\CarbonInterface;
use DateTime;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use IntlDateFormatter;
use RRule\RRule;

class RepeatService
{
    /**
     * @return RRule<DateTime>|null
     */
    public static function generateRecurringRule(Schedule|Event $repeatable): ?RRule
    {
        $payload = [
            'DTSTART' => $repeatable->start->toDateTime(),
            'FREQ' => $repeatable->frequency instanceof BackedEnum
                ? $repeatable->frequency->value
                : $repeatable->frequency,
            'INTERVAL' => $repeatable->interval,
        ];

        switch ($repeatable->frequency) {
            case ScheduleFrequency::WEEKLY:
                if ($repeatable->by_day?->isNotEmpty()) {
                    $payload['BYDAY'] = $repeatable->by_day->implode(',');
                }
                break;

            case ScheduleFrequency::MONTHLY:
                if ($repeatable->by_day && $repeatable->by_set_position) {
                    $payload['BYDAY'] = $repeatable->by_day;
                    $payload['BYSETPOS'] = $repeatable->by_set_position;
                } elseif ($repeatable->by_month_day?->isNotEmpty()) {
                    $payload['BYMONTHDAY'] = $repeatable->by_month_day->implode(',');
                }
                break;

            case ScheduleFrequency::YEARLY:
                if ($repeatable->by_month?->isNotEmpty()) {
                    $payload['BYMONTH'] = $repeatable->by_month->implode(',');
                }

                if ($repeatable->by_day && $repeatable->by_set_position) {
                    $payload['BYDAY'] = $repeatable->by_day;
                    $payload['BYSETPOS'] = $repeatable->by_set_position;
                }
                break;
        }

        if ($repeatable->count && $repeatable->end_type === ScheduleEndType::AFTER) {
            $payload['COUNT'] = $repeatable->count;
        }

        if ($repeatable->until && $repeatable->end_type === ScheduleEndType::ON) {
            $payload['UNTIL'] = $repeatable->until->toDateString();
        }

        return rescue(fn () => new RRule($payload));
    }

    public static function lastOccurrence(Schedule $repeatable): ?Carbon
    {
        if ($repeatable->end_type === 'on' && filled($repeatable->until)) {
            return $repeatable->until;
        }

        $rule = RepeatService::generateRecurringRule($repeatable);

        if (is_null($rule) || blank($repeatable->count)) {
            return null;
        }

        $lastOccurrence = $rule->getNthOccurrenceAfter($repeatable->start, $repeatable->count);

        if (is_null($lastOccurrence)) {
            return null;
        }

        return Carbon::parse($lastOccurrence);
    }

    public static function nextOccurrence(Schedule $repeatable): ?Carbon
    {
        $rule = RepeatService::generateRecurringRule($repeatable);

        if (is_null($rule)) {
            return null;
        }

        // We need to sub one minute, so we can actually do minute-by-minute
        // comparisons to now without it being kicked to the next occurrence.
        $nextOccurrence = collect($rule->getOccurrencesAfter(now()->subMinute(), true, 1))->first();

        if (is_null($nextOccurrence)) {
            return null;
        }

        return Carbon::parse($nextOccurrence);
    }

    /**
     * @return array<CarbonInterface>|null
     */
    public static function occurrenceBetween(Schedule $schedule, CarbonInterface $start, CarbonInterface $end): ?array
    {
        $rule = RepeatService::generateRecurringRule($schedule);

        if (is_null($rule)) {
            return null;
        }

        return collect($rule->getOccurrencesBetween($start, $end))->map(function (DateTime $dateTime) {
            return Carbon::parse($dateTime);
        })->toArray();
    }

    public static function getSchedulePattern(Schedule $schedule, bool $allDay = false): ?string
    {
        $rule = RepeatService::generateRecurringRule($schedule);

        if (is_null($rule)) {
            return null;
        }

        return Str::ucwords($rule->humanReadable([
            'date_formatter' => function ($date) use ($allDay) {
                $formatter = IntlDateFormatter::create(
                    locale: config('app.locale'),
                    dateType: IntlDateFormatter::LONG,
                    timeType: $allDay
                        ? IntlDateFormatter::NONE
                        : IntlDateFormatter::MEDIUM,
                    timezone: UserSettingsService::get('timezone', config('app.timezone'))
                );

                return $formatter->format($date);
            },

        ]));
    }
}
