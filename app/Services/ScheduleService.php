<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Enums\ScheduleEndType;
use App\Models\Enums\ScheduleFrequency;
use App\Models\Schedule;
use App\Settings\OrganizationSettings;
use Carbon\CarbonInterface;
use DateTime;
use DateTimeInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use IntlDateFormatter;
use RRule\RRule;

class ScheduleService
{
    /**
     * The rule is expanded in the organization's timezone so that occurrences keep the same
     * local time and weekday across daylight saving changes.
     *
     * @return RRule<DateTime>|null
     */
    public static function generateRecurringRule(Schedule $repeatable): ?RRule
    {
        $payload = [
            'DTSTART' => $repeatable->start->copy()->setTimezone(static::timezone())->toDateTime(),
            'FREQ' => $repeatable->frequency->value,
            'INTERVAL' => max(1, $repeatable->interval),
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

        return rescue(fn (): RRule => new RRule($payload));
    }

    public static function lastOccurrence(Schedule $repeatable): ?Carbon
    {
        if ($repeatable->end_type === ScheduleEndType::NEVER) {
            return null;
        }

        if ($repeatable->end_type === ScheduleEndType::ON && filled($repeatable->until)) {
            return $repeatable->until;
        }

        $rule = ScheduleService::generateRecurringRule($repeatable);

        if (is_null($rule) || blank($repeatable->count)) {
            return null;
        }

        $lastOccurrence = collect($rule->getOccurrences())->last();

        if (is_null($lastOccurrence)) {
            return null;
        }

        return static::toApplicationTime($lastOccurrence);
    }

    public static function nextOccurrence(Schedule $repeatable): ?Carbon
    {
        $rule = ScheduleService::generateRecurringRule($repeatable);

        if (is_null($rule)) {
            return null;
        }

        // We need to sub one minute, so we can actually do minute-by-minute
        // comparisons to now without it being kicked to the next occurrence.
        $nextOccurrence = collect($rule->getOccurrencesAfter(now()->subMinute(), true, 1))->first();

        if (is_null($nextOccurrence)) {
            return null;
        }

        return static::toApplicationTime($nextOccurrence);
    }

    /**
     * The latest occurrence that starts at or before the given time.
     */
    public static function previousOccurrence(Schedule $schedule, CarbonInterface $at): ?Carbon
    {
        $occurrence = collect(ScheduleService::generateRecurringRule($schedule)?->getOccurrencesBefore($at, true, 1) ?? [])->last();

        return $occurrence instanceof DateTimeInterface ? static::toApplicationTime($occurrence) : null;
    }

    /**
     * The earliest occurrence that starts after the given time.
     */
    public static function followingOccurrence(Schedule $schedule, CarbonInterface $at): ?Carbon
    {
        $occurrence = collect(ScheduleService::generateRecurringRule($schedule)?->getOccurrencesAfter($at, false, 1) ?? [])->first();

        return $occurrence instanceof DateTimeInterface ? static::toApplicationTime($occurrence) : null;
    }

    /**
     * @return array<Carbon>|null
     */
    public static function occurrenceBetween(Schedule $schedule, CarbonInterface $start, CarbonInterface $end): ?array
    {
        $rule = ScheduleService::generateRecurringRule($schedule);

        if (is_null($rule)) {
            return null;
        }

        return collect($rule->getOccurrencesBetween($start, $end))->map(fn (DateTime $dateTime): Carbon => static::toApplicationTime($dateTime))->toArray();
    }

    public static function getSchedulePattern(Schedule $schedule, bool $allDay = false): ?string
    {
        $rule = ScheduleService::generateRecurringRule($schedule);

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
                    timezone: UserSettingsService::get('timezone', function () {
                        /** @var OrganizationSettings $settings */
                        $settings = resolve(OrganizationSettings::class);

                        return $settings->timezone ?? config('app.timezone');
                    })
                );

                return $formatter->format($date);
            },

        ]));
    }

    /**
     * The timezone schedules are expanded in.
     */
    public static function timezone(): string
    {
        return rescue(function (): string {
            /** @var OrganizationSettings $settings */
            $settings = resolve(OrganizationSettings::class);

            return $settings->timezone ?? config('app.timezone');
        }, config('app.timezone'), report: false);
    }

    protected static function toApplicationTime(DateTimeInterface $dateTime): Carbon
    {
        return Date::instance($dateTime)->setTimezone(config('app.timezone'));
    }
}
