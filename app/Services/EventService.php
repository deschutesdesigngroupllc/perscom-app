<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Event;
use RRule\RRule;

class EventService
{
    public static function generateRecurringRule(Event $event): ?RRule
    {
        $payload = [
            'DTSTART' => $event->start->toDateTimeString(),
            'FREQ' => $event->frequency,
            'INTERVAL' => $event->interval,
        ];

        switch ($event->frequency) {
            case 'WEEKLY':
                if ($event->by_day?->isNotEmpty()) {
                    $payload['BYDAY'] = $event->by_day->implode(',');
                }
                break;

            case 'MONTHLY':
                if ($event->by_day && $event->by_set_position) {
                    $payload['BYDAY'] = $event->by_day;
                    $payload['BYSETPOS'] = $event->by_set_position;
                } elseif ($event->by_month_day?->isNotEmpty()) {
                    $payload['BYMONTHDAY'] = $event->by_month_day->implode(',');
                }
                break;

            case 'YEARLY':
                if ($event->by_month?->isNotEmpty()) {
                    $payload['BYMONTH'] = $event->by_month->implode(',');
                }

                if ($event->by_day && $event->by_set_position) {
                    $payload['BYDAY'] = $event->by_day;
                    $payload['BYSETPOS'] = $event->by_set_position;
                }
                break;
        }

        if ($event->count && $event->end_type === 'after') {
            $payload['COUNT'] = $event->count;
        }

        if ($event->until && $event->end_type === 'on') {
            $payload['UNTIL'] = $event->until->toDateString();
        }

        return $event->repeats ? new RRule($payload) : null;
    }
}
