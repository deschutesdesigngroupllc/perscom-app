<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Support\ICalendar;

use App\Support\ICalendar\Calendar;
use App\Support\ICalendar\Component;
use Illuminate\Support\Facades\Date;
use Tests\Feature\Tenant\TenantTestCase;

class ComponentTest extends TenantTestCase
{
    public function test_text_values_are_escaped(): void
    {
        $this->assertSame('Alpha\\, Bravo\; Charlie \\\\ Delta\\nEcho\\nFoxtrot', Component::escape("Alpha, Bravo; Charlie \\ Delta\r\nEcho\nFoxtrot"));
    }

    public function test_long_lines_are_folded_at_75_octets(): void
    {
        $line = 'DESCRIPTION:'.str_repeat('a', 200);
        $folded = Component::fold($line);
        $parts = explode("\r\n", $folded);

        $this->assertCount(3, $parts);
        $this->assertSame(75, strlen($parts[0]));
        $this->assertSame(75, strlen($parts[1]));
        $this->assertStringStartsWith(' ', $parts[1]);
        $this->assertSame($line, str_replace("\r\n ", '', $folded));
    }

    public function test_folding_never_splits_multibyte_characters(): void
    {
        $line = 'SUMMARY:'.str_repeat('é', 100);

        collect(explode("\r\n", Component::fold($line)))->each(function (string $part): void {
            $this->assertLessThanOrEqual(75, strlen($part));
            $this->assertTrue(mb_check_encoding($part, 'UTF-8'));
        });
    }

    public function test_calendar_renders_crlf_terminated_lines_with_headers(): void
    {
        $output = Calendar::create('Bravo Company, Events', 'America/Chicago')
            ->component(Component::make('vevent')
                ->property('UID', 'event-1@example.com')
                ->dateTime('DTSTART', Date::parse('2026-06-04 20:00:00', 'UTC'))
                ->dateTime('DTEND', Date::parse('2026-06-04 21:00:00', 'UTC'), 'America/Chicago')
                ->date('DTSTAMP', Date::parse('2026-06-04'))
                ->text('LOCATION', null))
            ->render();

        $this->assertStringStartsWith("BEGIN:VCALENDAR\r\nPRODID:", $output);
        $this->assertStringEndsWith("END:VCALENDAR\r\n", $output);
        $this->assertStringContainsString("VERSION:2.0\r\n", $output);
        $this->assertStringContainsString("X-WR-CALNAME:Bravo Company\\, Events\r\n", $output);
        $this->assertStringContainsString("X-WR-TIMEZONE:America/Chicago\r\n", $output);
        $this->assertStringContainsString("BEGIN:VEVENT\r\nUID:event-1@example.com\r\n", $output);
        $this->assertStringContainsString("DTSTART:20260604T200000Z\r\n", $output);
        $this->assertStringContainsString("DTEND;TZID=America/Chicago:20260604T160000\r\n", $output);
        $this->assertStringContainsString("DTSTAMP;VALUE=DATE:20260604\r\n", $output);
        $this->assertStringNotContainsString('LOCATION', $output);
        $this->assertDoesNotMatchRegularExpression("/(?<!\r)\n/", $output);
    }
}
