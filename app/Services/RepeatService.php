<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Event;
use App\Models\Repeatable;
use Carbon\Carbon;
use DateTime;
use RRule\RRule;

class RepeatService
{
    /**
     * @return RRule<DateTime>
     */
    public static function generateRecurringRule(Repeatable|Event $repeatable): RRule
    {
        $payload = [
            'DTSTART' => $repeatable->start->toDateTimeString(),
            'FREQ' => $repeatable->frequency,
            'INTERVAL' => $repeatable->interval,
        ];

        switch ($repeatable->frequency) {
            case 'WEEKLY':
                if ($repeatable->by_day?->isNotEmpty()) {
                    $payload['BYDAY'] = $repeatable->by_day->implode(',');
                }
                break;

            case 'MONTHLY':
                if ($repeatable->by_day && $repeatable->by_set_position) {
                    $payload['BYDAY'] = $repeatable->by_day;
                    $payload['BYSETPOS'] = $repeatable->by_set_position;
                } elseif ($repeatable->by_month_day?->isNotEmpty()) {
                    $payload['BYMONTHDAY'] = $repeatable->by_month_day->implode(',');
                }
                break;

            case 'YEARLY':
                if ($repeatable->by_month?->isNotEmpty()) {
                    $payload['BYMONTH'] = $repeatable->by_month->implode(',');
                }

                if ($repeatable->by_day && $repeatable->by_set_position) {
                    $payload['BYDAY'] = $repeatable->by_day;
                    $payload['BYSETPOS'] = $repeatable->by_set_position;
                }
                break;
        }

        if ($repeatable->count && $repeatable->end_type === 'after') {
            $payload['COUNT'] = $repeatable->count;
        }

        if ($repeatable->until && $repeatable->end_type === 'on') {
            $payload['UNTIL'] = $repeatable->until->toDateString();
        }

        return new RRule($payload);
    }

    public static function lastOccurrence(Repeatable $repeatable): ?Carbon
    {
        if ($repeatable->end_type === 'on' && filled($repeatable->until)) {
            return $repeatable->until;
        }

        $rule = RepeatService::generateRecurringRule($repeatable);

        if (blank($rule) || blank($repeatable->count)) {
            return null;
        }

        $lastOccurrence = $rule->getNthOccurrenceAfter($repeatable->start, $repeatable->count);

        if (blank($lastOccurrence)) {
            return null;
        }

        return Carbon::parse($lastOccurrence);
    }

    public static function nextOccurrence(Repeatable $repeatable): ?Carbon
    {
        $rule = RepeatService::generateRecurringRule($repeatable);

        if (blank($rule)) {
            return null;
        }

        $nextOccurrence = collect($rule->getOccurrencesAfter(now(), false, 1))->first();

        if (blank($nextOccurrence)) {
            return null;
        }

        return Carbon::parse($nextOccurrence);
    }
}
