<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Services;

use App\Models\Enums\ScheduleEndType;
use App\Models\Enums\ScheduleFrequency;
use App\Models\Event;
use App\Models\EventOccurrence;
use App\Models\Schedule;
use App\Models\User;
use App\Services\CalendarFeedService;
use App\Services\EventOccurrenceService;
use App\Services\ScheduleService;
use App\Services\SettingsService;
use App\Settings\OrganizationSettings;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Date;
use Tests\Feature\Tenant\TenantTestCase;

class CalendarFeedServiceTest extends TenantTestCase
{
    public function test_the_token_is_generated_once_and_can_be_reset(): void
    {
        $user = User::factory()->createQuietly();

        $token = CalendarFeedService::tokenFor($user);

        $this->assertSame(40, strlen($token));
        $this->assertSame($token, CalendarFeedService::tokenFor($user->fresh()));

        $reset = CalendarFeedService::resetToken($user);

        $this->assertNotSame($token, $reset);
        $this->assertNull(CalendarFeedService::findUser($token));
        $this->assertTrue(CalendarFeedService::findUser($reset)?->is($user));
    }

    public function test_the_token_is_hidden_from_serialized_users(): void
    {
        $user = User::factory()->createQuietly();
        CalendarFeedService::tokenFor($user);

        $this->assertArrayNotHasKey('calendar_feed_token', $user->fresh()->toArray());
    }

    public function test_subscribe_links_point_at_the_feed(): void
    {
        $url = 'https://acme.perscom.io/feeds/abc/calendar.ics';

        $this->assertSame('webcal://acme.perscom.io/feeds/abc/calendar.ics', CalendarFeedService::webcalUrl($url));
        $this->assertSame('https://calendar.google.com/calendar/r?cid=webcal%3A%2F%2Facme.perscom.io%2Ffeeds%2Fabc%2Fcalendar.ics', CalendarFeedService::googleSubscribeUrl($url));
        $this->assertStringStartsWith('https://outlook.live.com/calendar/0/addfromweb?url=https%3A%2F%2Facme.perscom.io', CalendarFeedService::outlookSubscribeUrl($url, 'Acme Events'));
        $this->assertStringEndsWith('&name=Acme%20Events', CalendarFeedService::outlookSubscribeUrl($url, 'Acme Events'));
    }

    public function test_one_time_events_are_written_in_utc(): void
    {
        $event = Event::factory()->createQuietly([
            'name' => 'Range Day, Phase 1',
            'description' => '<p>Bring eye; ear protection.</p>',
            'location' => 'Range 4',
            'url' => null,
            'all_day' => false,
            'repeats' => false,
            'starts' => Date::parse('2026-06-04 20:00:00'),
            'ends' => Date::parse('2026-06-04 22:30:00'),
        ]);

        $ics = CalendarFeedService::render([$event], 'Events');

        $this->assertStringContainsString(sprintf('UID:event-%s-', $event->getKey()), $ics);
        $this->assertStringContainsString("DTSTART:20260604T200000Z\r\n", $ics);
        $this->assertStringContainsString("DTEND:20260604T223000Z\r\n", $ics);
        $this->assertStringContainsString("SUMMARY:Range Day\\, Phase 1\r\n", $ics);
        $this->assertStringContainsString('DESCRIPTION:Bring eye\; ear protection.\\n\\n', $ics);
        $this->assertStringContainsString("LOCATION:Range 4\r\n", $ics);
        $this->assertStringContainsString("STATUS:CONFIRMED\r\n", $ics);
        $this->assertStringNotContainsString('RRULE', $ics);
    }

    public function test_all_day_events_use_dates(): void
    {
        $event = Event::factory()->createQuietly([
            'all_day' => true,
            'repeats' => false,
            'starts' => Date::parse('2026-06-04'),
            'ends' => Date::parse('2026-06-05'),
        ]);

        $ics = CalendarFeedService::render([$event->fresh()], 'Events');

        $this->assertStringContainsString("DTSTART;VALUE=DATE:20260604\r\n", $ics);
        $this->assertStringContainsString("DTEND;VALUE=DATE:20260606\r\n", $ics);
    }

    public function test_repeating_events_are_written_with_a_rule_and_cancelled_dates_excluded(): void
    {
        $this->travelTo(Date::parse('2026-06-01 12:00:00'));
        $timezone = ScheduleService::timezone();
        $event = $this->makeWeeklyEvent(Date::parse('2026-05-07 20:00:00'));
        $cancelled = Date::parse('2026-06-11 20:00:00');
        EventOccurrence::factory()->for($event)->cancelled()->createQuietly(['starts' => $cancelled, 'ends' => $cancelled->copy()->addHour()]);

        $ics = CalendarFeedService::render([$event->fresh()], 'Events');

        $this->assertStringContainsString(sprintf("DTSTART;TZID=%s:%s\r\n", $timezone, $this->local(Date::parse('2026-05-07 20:00:00'), $timezone)), $ics);
        $this->assertStringContainsString(sprintf("DTEND;TZID=%s:%s\r\n", $timezone, $this->local(Date::parse('2026-05-07 21:00:00'), $timezone)), $ics);
        $this->assertMatchesRegularExpression('/\r\nRRULE:FREQ=WEEKLY[^\r]*\r\n/', $ics);
        $this->assertStringContainsString(sprintf("EXDATE;TZID=%s:%s\r\n", $timezone, $this->local($cancelled, $timezone)), $ics);
        $this->assertSame(1, substr_count($ics, 'BEGIN:VEVENT'));
    }

    public function test_repeating_events_keep_local_time_in_the_organization_timezone(): void
    {
        $this->travelTo(Date::parse('2026-06-01 12:00:00'));
        $settings = resolve(OrganizationSettings::class);
        $settings->timezone = 'America/Chicago';
        $settings->save();
        SettingsService::flush(OrganizationSettings::class);

        $event = $this->makeWeeklyEvent(Date::parse('2026-05-07 20:00:00'));

        $ics = CalendarFeedService::render([$event], 'Events');

        $this->assertStringContainsString("X-WR-TIMEZONE:America/Chicago\r\n", $ics);
        $this->assertStringContainsString("DTSTART;TZID=America/Chicago:20260507T150000\r\n", $ics);
    }

    public function test_occurrences_with_notes_are_written_as_overrides(): void
    {
        $this->travelTo(Date::parse('2026-06-01 12:00:00'));
        $event = $this->makeWeeklyEvent(Date::parse('2026-05-07 20:00:00'));
        $starts = Date::parse('2026-06-04 20:00:00');
        EventOccurrence::factory()->for($event)->createQuietly(['starts' => $starts, 'ends' => $starts->copy()->addHour(), 'notes' => 'Meet at the north gate.', 'cancelled_at' => null]);

        $ics = CalendarFeedService::render([$event->fresh()], 'Events');

        $this->assertSame(2, substr_count($ics, 'BEGIN:VEVENT'));
        $this->assertStringContainsString('RECURRENCE-ID;TZID=', $ics);
        $this->assertStringContainsString('Meet at the north gate.', str_replace("\r\n ", '', $ics));
    }

    public function test_google_template_links_describe_the_occurrence(): void
    {
        $event = Event::factory()->createQuietly([
            'name' => 'Range Day',
            'location' => 'Range 4',
            'all_day' => false,
            'repeats' => false,
            'starts' => Date::parse('2026-06-04 20:00:00'),
            'ends' => Date::parse('2026-06-04 21:00:00'),
        ]);

        $url = CalendarFeedService::googleTemplateUrl(EventOccurrenceService::nearest($event));

        $this->assertStringStartsWith('https://calendar.google.com/calendar/render?action=TEMPLATE&text=Range%20Day&dates=20260604T200000Z%2F20260604T210000Z', $url);
        $this->assertStringContainsString('location=Range%204', $url);
    }

    public function test_all_day_occurrences_use_their_calendar_date_east_of_utc(): void
    {
        OrganizationSettings::fake(['timezone' => 'Europe/Berlin']);

        $event = Event::factory()->createQuietly([
            'name' => 'Range Day',
            'all_day' => true,
            'repeats' => false,
            'starts' => Date::parse('2026-06-04'),
            'ends' => Date::parse('2026-06-04'),
        ])->refresh();

        $occurrence = EventOccurrenceService::nearest($event);

        $this->assertStringContainsString('dates=20260604%2F20260605', CalendarFeedService::googleTemplateUrl($occurrence));

        $ics = CalendarFeedService::renderOccurrence($occurrence);

        $this->assertStringContainsString("DTSTART;VALUE=DATE:20260604\r\n", $ics);
        $this->assertStringContainsString("DTEND;VALUE=DATE:20260605\r\n", $ics);
    }

    private function local(CarbonInterface $date, string $timezone): string
    {
        return $date->copy()->setTimezone($timezone)->format('Ymd\THis');
    }

    private function makeWeeklyEvent(CarbonInterface $start): Event
    {
        $event = Event::factory()->createQuietly(['all_day' => false, 'repeats' => true, 'starts' => $start, 'ends' => $start->copy()->addHour()]);
        Schedule::factory()->for($event, 'repeatable')->createQuietly(['start' => $start, 'duration' => 1, 'frequency' => ScheduleFrequency::WEEKLY, 'interval' => 1, 'end_type' => ScheduleEndType::NEVER]);

        return $event->refresh();
    }
}
