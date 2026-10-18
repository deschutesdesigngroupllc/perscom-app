<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\Events\OccurrenceData;
use App\Filament\App\Resources\EventResource;
use App\Models\Calendar;
use App\Models\Event;
use App\Models\EventOccurrence;
use App\Models\User;
use App\Settings\OrganizationSettings;
use App\Support\ICalendar\Calendar as ICalendar;
use App\Support\ICalendar\Component;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RRule\RRule;

/**
 * Personal calendar subscription feeds (iCalendar) and "add to calendar" links for events.
 */
class CalendarFeedService
{
    /**
     * How far back subscription feeds reach, in days.
     */
    public const int FEED_HISTORY_DAYS = 90;

    /**
     * The member's secret feed token, generated the first time it is needed.
     */
    public static function tokenFor(User $user): string
    {
        $token = $user->getAttribute('calendar_feed_token');

        if (is_string($token) && filled($token)) {
            return $token;
        }

        return static::resetToken($user);
    }

    /**
     * Replace the member's feed token, invalidating every feed URL issued before.
     */
    public static function resetToken(User $user): string
    {
        $token = Str::random(40);

        $user->forceFill(['calendar_feed_token' => $token])->saveQuietly();

        return $token;
    }

    public static function findUser(string $token): ?User
    {
        if (blank($token)) {
            return null;
        }

        return User::query()->where('calendar_feed_token', $token)->first();
    }

    /**
     * The member's feed of every event they can view, optionally limited to some calendars.
     *
     * @param  array<int, int>  $calendarIds
     */
    public static function personalFeedUrl(User $user, array $calendarIds = []): string
    {
        return route('app.calendar-feeds.personal', array_filter([
            'token' => static::tokenFor($user),
            'calendars' => implode(',', $calendarIds),
        ], filled(...)));
    }

    public static function calendarFeedUrl(User $user, Calendar $calendar): string
    {
        return route('app.calendar-feeds.calendar', [
            'token' => static::tokenFor($user),
            'calendar' => $calendar->getKey(),
        ]);
    }

    public static function webcalUrl(string $url): string
    {
        return (string) preg_replace('#^https?://#i', 'webcal://', $url);
    }

    public static function googleSubscribeUrl(string $url): string
    {
        return 'https://calendar.google.com/calendar/r?cid='.rawurlencode(static::webcalUrl($url));
    }

    public static function outlookSubscribeUrl(string $url, string $name): string
    {
        return 'https://outlook.live.com/calendar/0/addfromweb?'.http_build_query(['url' => $url, 'name' => $name], '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * A Google Calendar "create event" link pre-filled with the occurrence.
     */
    public static function googleTemplateUrl(OccurrenceData $occurrence): string
    {
        $dates = $occurrence->allDay
            ? sprintf('%s/%s', Component::formatDate($occurrence->localStarts()), Component::formatDate($occurrence->localEnds()->startOfDay()->addDay()))
            : sprintf('%s/%s', Component::formatUtcDateTime($occurrence->starts), Component::formatUtcDateTime($occurrence->ends));

        return 'https://calendar.google.com/calendar/render?'.http_build_query(array_filter([
            'action' => 'TEMPLATE',
            'text' => $occurrence->event->name,
            'dates' => $dates,
            'details' => static::description($occurrence->event),
            'location' => static::location($occurrence->event),
        ], filled(...)), '', '&', PHP_QUERY_RFC3986);
    }

    public static function personalFeedName(): string
    {
        return sprintf('%s Events', static::organizationName());
    }

    public static function calendarFeedName(Calendar $calendar): string
    {
        return sprintf('%s: %s', static::organizationName(), $calendar->name);
    }

    /**
     * Events that belong in a subscription feed: one-time events that ended in the last
     * FEED_HISTORY_DAYS days or later, and repeating events whose schedule has not ended before then.
     *
     * @param  array<int, int>  $calendarIds
     * @return Collection<int, Event>
     */
    public static function feedEvents(User $user, array $calendarIds = []): Collection
    {
        $from = now()->subDays(static::FEED_HISTORY_DAYS);

        return Event::query()
            ->with(['calendar', 'schedule', 'occurrences'])
            ->when(filled($calendarIds), fn (Builder $query): Builder => $query->whereIn('calendar_id', $calendarIds))
            ->where(fn (Builder $query): Builder => $query
                ->where(fn (Builder $query): Builder => $query
                    ->where('repeats', false)
                    ->whereRaw('COALESCE(ends, starts) >= ?', [$from]))
                ->orWhere(fn (Builder $query): Builder => $query
                    ->where('repeats', true)
                    ->whereHas('schedule', fn (Builder $query): Builder => $query
                        ->whereNull('last_occurrence')
                        ->orWhere('last_occurrence', '>=', $from))))
            ->orderBy('starts')
            ->get()
            ->filter(fn (Event $event): bool => $user->can('view', $event))
            ->values();
    }

    /**
     * Render a VCALENDAR document for the events.
     *
     * @param  iterable<int, Event>  $events
     */
    public static function render(iterable $events, string $name): string
    {
        return ICalendar::create($name, ScheduleService::timezone())
            ->components(collect($events)->flatMap(fn (Event $event): Collection => static::components($event)))
            ->render();
    }

    /**
     * Render a VCALENDAR document holding a single occurrence of an event.
     */
    public static function renderOccurrence(OccurrenceData $occurrence): string
    {
        $event = $occurrence->event;
        $component = static::baseComponent($event, static::uid($event, $event->repeats ? $occurrence->starts : null), $occurrence->occurrence);

        static::schedule($component, $occurrence->starts, $occurrence->ends, $occurrence->allDay, $occurrence->allDay ? ScheduleService::timezone() : null);

        return ICalendar::create($event->name, ScheduleService::timezone())
            ->component($component)
            ->render();
    }

    /**
     * The VEVENT components for an event: one for a one-time event, or the recurring series plus
     * any overridden occurrences for a repeating event.
     *
     * @return Collection<int, Component>
     */
    public static function components(Event $event): Collection
    {
        /** @var Collection<int, EventOccurrence> $occurrences */
        $occurrences = $event->relationLoaded('occurrences') ? $event->getRelation('occurrences') : $event->occurrences()->get();

        if (! $event->repeats) {
            $starts = $event->starts->copy();
            $occurrence = $occurrences->first(fn (EventOccurrence $occurrence): bool => $occurrence->starts->equalTo($starts));
            $component = static::baseComponent($event, static::uid($event), $occurrence);

            return collect([static::schedule($component, $starts, ($event->ends ?? $event->starts)->copy(), $event->all_day)]);
        }

        if (blank($event->schedule) || ! ($rule = ScheduleService::generateRecurringRule($event->schedule)) instanceof RRule) {
            return collect();
        }

        $timezone = ScheduleService::timezone();
        $starts = $event->schedule->start->copy();
        $duration = (int) $event->schedule->duration;
        $ends = $duration > 0 || ! $event->all_day ? $starts->copy()->addHours($duration) : $starts->copy()->endOfDay();

        $series = static::baseComponent($event, static::uid($event));
        static::schedule($series, $starts, $ends, $event->all_day, $timezone);
        $series->property('RRULE', static::recurrenceRule($rule->rfcString(), $event->all_day));

        $cancelled = $occurrences->filter(fn (EventOccurrence $occurrence): bool => $occurrence->is_cancelled);

        if ($cancelled->isNotEmpty()) {
            $event->all_day
                ? $series->property('EXDATE', $cancelled->map(fn (EventOccurrence $occurrence): string => Component::formatDate($occurrence->starts->copy()->setTimezone($timezone)))->implode(','), ['VALUE' => 'DATE'])
                : $series->property('EXDATE', $cancelled->map(fn (EventOccurrence $occurrence): string => Component::formatLocalDateTime($occurrence->starts, $timezone))->implode(','), ['TZID' => $timezone]);
        }

        $overrides = $occurrences
            ->reject(fn (EventOccurrence $occurrence): bool => $occurrence->is_cancelled)
            ->filter(fn (EventOccurrence $occurrence): bool => filled($occurrence->notes)
                || ($occurrence->ends instanceof CarbonInterface && ! $occurrence->ends->equalTo($occurrence->starts->copy()->addHours($duration)) && ! $event->all_day))
            ->map(function (EventOccurrence $occurrence) use ($event, $timezone): Component {
                $component = static::baseComponent($event, static::uid($event), $occurrence);

                $event->all_day
                    ? $component->date('RECURRENCE-ID', $occurrence->starts->copy()->setTimezone($timezone))
                    : $component->dateTime('RECURRENCE-ID', $occurrence->starts, $timezone);

                return static::schedule($component, $occurrence->starts, $occurrence->ends ?? $occurrence->starts, $event->all_day, $timezone);
            });

        return collect([$series])->merge($overrides)->values();
    }

    public static function toResponse(string $calendar, string $filename, bool $download = false): Response
    {
        return response($calendar, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => sprintf('%s; filename="%s"', $download ? 'attachment' : 'inline', $filename),
            'Cache-Control' => 'private, max-age=900',
            'X-Robots-Tag' => 'noindex',
        ]);
    }

    /**
     * Plain-text description for calendar apps, ending with a link back to the event page.
     */
    public static function description(Event $event, ?EventOccurrence $occurrence = null): string
    {
        return collect([
            static::plainText($event->description),
            static::plainText($occurrence?->notes),
            static::eventPageUrl($event),
        ])
            ->filter(fn (?string $value): bool => filled($value))
            ->implode("\n\n");
    }

    public static function eventPageUrl(Event $event): string
    {
        return EventResource::getUrl('view', ['record' => $event], panel: 'app');
    }

    protected static function baseComponent(Event $event, string $uid, ?EventOccurrence $occurrence = null): Component
    {
        $externalUrl = $event->getRawOriginal('url');

        return Component::make('VEVENT')
            ->property('UID', $uid)
            ->dateTime('DTSTAMP', now())
            ->text('SUMMARY', $event->name)
            ->text('DESCRIPTION', static::description($event, $occurrence))
            ->text('LOCATION', static::location($event))
            ->property('URL', is_string($externalUrl) && filled($externalUrl) ? $externalUrl : static::eventPageUrl($event), ['VALUE' => 'URI'])
            ->text('CATEGORIES', $event->calendar?->name)
            ->property('STATUS', $occurrence?->is_cancelled ? 'CANCELLED' : 'CONFIRMED')
            ->when($event->updated_at, fn (Component $component, CarbonInterface $updatedAt): Component => $component->dateTime('LAST-MODIFIED', $updatedAt));
    }

    /**
     * Write DTSTART/DTEND: dates for all-day events, otherwise UTC date-times, or local
     * date-times with a TZID when a timezone is given (used by recurring series so DST is kept).
     */
    protected static function schedule(Component $component, CarbonInterface $starts, CarbonInterface $ends, bool $allDay, ?string $timezone = null): Component
    {
        if ($allDay) {
            $localStarts = $timezone ? $starts->copy()->setTimezone($timezone) : $starts->copy();
            $localEnds = $timezone ? $ends->copy()->setTimezone($timezone) : $ends->copy();

            return $component
                ->date('DTSTART', $localStarts)
                ->date('DTEND', $localEnds->startOfDay()->addDay()->max($localStarts->copy()->startOfDay()->addDay()));
        }

        return $component
            ->dateTime('DTSTART', $starts, $timezone)
            ->dateTime('DTEND', $ends->greaterThan($starts) ? $ends : $starts, $timezone);
    }

    /**
     * Extract the RRULE value from the rule's RFC string. All-day series use a DATE UNTIL so it
     * matches their DATE DTSTART.
     */
    protected static function recurrenceRule(string $rfcString, bool $allDay): string
    {
        $rule = Str::of($rfcString)
            ->explode("\n")
            ->map(fn (string $line): string => trim($line))
            ->first(fn (string $line): bool => Str::startsWith($line, 'RRULE:'), '');

        $rule = Str::after($rule, 'RRULE:');

        return $allDay ? (string) preg_replace('/UNTIL=(\d{8})T\d{6}Z?/', 'UNTIL=$1', $rule) : $rule;
    }

    protected static function uid(Event $event, ?CarbonInterface $occurrence = null): string
    {
        $host = parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'perscom.io';
        $tenant = tenant()?->getTenantKey();

        return $occurrence instanceof CarbonInterface
            ? sprintf('event-%s-%s%s@%s', $event->getKey(), $occurrence->getTimestamp(), $tenant ? '-'.$tenant : '', $host)
            : sprintf('event-%s%s@%s', $event->getKey(), $tenant ? '-'.$tenant : '', $host);
    }

    protected static function location(Event $event): ?string
    {
        return filled($event->location) ? Str::squish($event->location) : null;
    }

    protected static function plainText(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        return Str::of(html_entity_decode(strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>'], "\n", $value)), ENT_QUOTES | ENT_HTML5))
            ->replaceMatches('/\n{3,}/', "\n\n")
            ->trim()
            ->toString();
    }

    protected static function organizationName(): string
    {
        return rescue(function (): string {
            /** @var OrganizationSettings $settings */
            $settings = resolve(OrganizationSettings::class);

            return filled($settings->name) ? $settings->name : (string) config('app.name');
        }, (string) config('app.name'), report: false);
    }
}
