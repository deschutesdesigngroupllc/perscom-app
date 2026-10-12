<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Resources;

use App\Filament\App\Resources\EventResource\Pages\CreateEvent;
use App\Filament\App\Resources\EventResource\Pages\EditEvent;
use App\Models\Calendar;
use App\Models\Enums\NotificationChannel;
use App\Models\Enums\NotificationInterval;
use App\Models\Enums\ScheduleEndType;
use App\Models\Enums\ScheduleFrequency;
use App\Models\Event;
use App\Models\Schedule;
use App\Models\User;
use App\Services\UserSettingsService;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Support\Facades\Date;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class EventResourceFormTest extends TenantTestCase
{
    public function test_it_creates_an_event(): void
    {
        $user = $this->actingAsSuperAdmin();
        $calendar = Calendar::factory()->createQuietly();

        Livewire::test(CreateEvent::class)
            ->assertSee('Basics')
            ->assertSee('When &amp; Where', false)
            ->assertSee('Registration &amp; Reminders', false)
            ->fillForm([
                'name' => 'Field Exercise',
                'calendar_id' => $calendar->getKey(),
                'author_id' => $user->getKey(),
                'starts' => '2026-07-01 18:00:00',
                'ends' => '2026-07-01 20:00:00',
                'all_day' => false,
                'location' => 'Training Area 4',
                'registration_enabled' => true,
            ])
            ->assertSee('Preview')
            ->assertSee('Field Exercise')
            ->assertSee('RSVPs on')
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('events', [
            'name' => 'Field Exercise',
            'calendar_id' => $calendar->getKey(),
            'author_id' => $user->getKey(),
            'location' => 'Training Area 4',
            'registration_enabled' => true,
            'repeats' => false,
        ]);
    }

    public function test_it_saves_every_section_of_the_edit_form(): void
    {
        $user = $this->actingAsSuperAdmin();
        $calendar = Calendar::factory()->createQuietly();
        $event = Event::factory()->createQuietly(['repeats' => false, 'all_day' => false, 'notifications_enabled' => false, 'registration_enabled' => false]);
        $timezone = UserSettingsService::timezone();

        Livewire::test(EditEvent::class, ['record' => $event->getKey()])
            ->fillForm([
                'name' => 'Weekly Drill',
                'calendar_id' => $calendar->getKey(),
                'author_id' => $user->getKey(),
                'description' => '<p>Weekly training night.</p>',
                'content' => '<p>Full briefing.</p>',
                'starts' => '2026-07-02 19:00:00',
                'ends' => '2026-07-02 21:00:00',
                'all_day' => false,
                'repeats' => true,
                'schedule.frequency' => ScheduleFrequency::WEEKLY->value,
                'schedule.interval' => 1,
                'schedule.by_day' => ['TH'],
                'schedule.end_type' => ScheduleEndType::AFTER->value,
                'schedule.count' => 10,
                'location' => 'Armory',
                'url' => 'https://example.com/drill',
                'registration_enabled' => true,
                'registration_deadline' => '2026-07-01 12:00:00',
                'creates_training_record' => true,
                'notifications_enabled' => true,
                'notifications_interval' => [NotificationInterval::PT1H->value],
                'notifications_channels' => [NotificationChannel::MAIL->value],
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $event->refresh();

        $this->assertSame('Weekly Drill', $event->name);
        $this->assertSame($calendar->getKey(), $event->calendar_id);
        $this->assertSame('<p>Full briefing.</p>', $event->content);
        $this->assertSame('Armory', $event->location);
        $this->assertSame('https://example.com/drill', $event->getRawOriginal('url'));
        $this->assertTrue($event->repeats);
        $this->assertTrue($event->registration_enabled);
        $this->assertTrue($event->creates_training_record);
        $this->assertTrue($event->notifications_enabled);
        $this->assertSame([NotificationInterval::PT1H->value], $event->notifications_interval);
        $this->assertEquals([NotificationChannel::MAIL], $event->notifications_channels->all());
        $this->assertTrue(Date::parse('2026-07-02 19:00:00', $timezone)->equalTo($event->starts));
        $this->assertTrue(Date::parse('2026-07-01 12:00:00', $timezone)->equalTo($event->registration_deadline));

        $schedule = $event->schedule;

        $this->assertInstanceOf(Schedule::class, $schedule);
        $this->assertSame(ScheduleFrequency::WEEKLY, $schedule->frequency);
        $this->assertSame(ScheduleEndType::AFTER, $schedule->end_type);
        $this->assertSame(10, (int) $schedule->count);
        $this->assertSame(['TH'], $schedule->by_day?->all());
    }

    public function test_the_image_upload_is_only_shown_when_editing(): void
    {
        $this->actingAsSuperAdmin();
        $event = Event::factory()->createQuietly();

        Livewire::test(CreateEvent::class)
            ->assertFormFieldDoesNotExist('image.path');

        Livewire::test(EditEvent::class, ['record' => $event->getKey()])
            ->assertFormFieldExists('image.path');
    }

    private function actingAsSuperAdmin(): User
    {
        $user = User::factory()->createQuietly();
        $user->assignRole(Utils::getSuperAdminName());

        $this->actingAs($user);

        return $user;
    }
}
