<?php

declare(strict_types=1);

namespace App\Support\ICalendar;

/**
 * A VCALENDAR document published to calendar apps.
 */
class Calendar extends Component
{
    public static function create(string $name, string $timezone): static
    {
        return static::make('VCALENDAR')
            ->property('PRODID', '-//PERSCOM//Events//EN')
            ->property('VERSION', '2.0')
            ->property('CALSCALE', 'GREGORIAN')
            ->property('METHOD', 'PUBLISH')
            ->text('X-WR-CALNAME', $name)
            ->property('X-WR-TIMEZONE', $timezone)
            ->property('REFRESH-INTERVAL', 'PT1H', ['VALUE' => 'DURATION'])
            ->property('X-PUBLISHED-TTL', 'PT1H');
    }
}
