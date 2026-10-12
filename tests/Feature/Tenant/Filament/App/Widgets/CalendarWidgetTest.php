<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Widgets;

use App\Filament\App\Widgets\CalendarWidget;
use App\Models\Enums\ScheduleEndType;
use App\Models\Enums\ScheduleFrequency;
use App\Models\Event;
use App\Models\EventOccurrence;
use App\Models\Schedule;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Illuminate\Support\Facades\Date;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class CalendarWidgetTest extends TenantTestCase
{
    public function test_it_shows_each_occurrence_and_hides_cancelled_ones(): void
    {
        $this->actingAsSuperAdmin();

        $start = Date::parse('2026-06-04 20:00:00');
        $event = Event::factory()->createQuietly(['name' => 'Weekly Ops', 'repeats' => true, 'all_day' => false, 'starts' => $start, 'ends' => $start->copy()->addHour()]);
        Schedule::factory()->for($event, 'repeatable')->createQuietly(['start' => $start, 'duration' => 2, 'frequency' => ScheduleFrequency::WEEKLY, 'interval' => 1, 'end_type' => ScheduleEndType::NEVER]);
        EventOccurrence::factory()->for($event)->cancelled()->createQuietly(['starts' => Date::parse('2026-06-11 20:00:00'), 'ends' => Date::parse('2026-06-11 22:00:00')]);
        Event::factory()->createQuietly(['name' => 'Old Event', 'repeats' => false, 'all_day' => false, 'starts' => Date::parse('2026-01-01 10:00:00'), 'ends' => Date::parse('2026-01-01 11:00:00')]);

        $widget = Livewire::test(CalendarWidget::class)->instance();
        $this->assertInstanceOf(CalendarWidget::class, $widget);

        /** @var array<int, CalendarEvent> $events */
        $events = (fn (): array => $this->getEvents(['start' => '2026-06-01', 'end' => '2026-06-21']))->call($widget);

        $this->assertSame(['Weekly Ops', 'Weekly Ops'], collect($events)->map(fn (CalendarEvent $calendarEvent): string => (string) $calendarEvent->getTitle())->all());
        $this->assertSame(['2026-06-04 20:00', '2026-06-18 20:00'], collect($events)->map(fn (CalendarEvent $calendarEvent): string => $calendarEvent->getStart()->utc()->format('Y-m-d H:i'))->all());
        $this->assertSame('2026-06-04 22:00', $events[0]->getEnd()->utc()->format('Y-m-d H:i'));
    }

    private function actingAsSuperAdmin(): User
    {
        $user = User::factory()->createQuietly();
        $user->assignRole(Utils::getSuperAdminName());

        $this->actingAs($user);

        return $user;
    }
}
