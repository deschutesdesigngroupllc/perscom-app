<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Enums\ScheduleEndType;
use App\Models\Enums\ScheduleFrequency;
use App\Models\Event;
use App\Models\Schedule;
use App\Settings\OrganizationSettings;
use Carbon\CarbonInterface;
use DateTime;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use IntlDateFormatter;
use RRule\RRule;

class ScheduleService
{
    /**
     * @return RRule<DateTime>|null
     */
    public static function generateRecurringRule(Schedule|Event $repeatable): ?RRule
    {
        $payload = [
            'DTSTART' => $repeatable->start->toDateTime(),
            'FREQ' => $repeatable->frequency->value,
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

        return Carbon::parse($lastOccurrence);
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

        return Carbon::parse($nextOccurrence);
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

        return collect($rule->getOccurrencesBetween($start, $end))->map(fn (DateTime $dateTime): Carbon => Carbon::parse($dateTime))->toArray();
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
                        $settings = app(OrganizationSettings::class);

                        return $settings->timezone ?? config('app.timezone');
                    })
                );

                return $formatter->format($date);
            },

        ]));
    }
}
