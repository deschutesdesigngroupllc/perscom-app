<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Actions;

use App\Filament\App\Pages\Calendar as CalendarPage;
use App\Models\Calendar;
use App\Models\User;
use App\Services\CalendarFeedService;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class SubscribeToCalendarActionTest extends TenantTestCase
{
    public function test_the_subscribe_action_shows_the_members_feed_links(): void
    {
        $user = $this->member();
        $this->actingAs($user);

        Livewire::test(CalendarPage::class)
            ->assertActionVisible('subscribeToCalendar')
            ->mountAction('subscribeToCalendar')
            ->assertMountedActionModalSee([
                'Subscribe to Calendar',
                CalendarFeedService::personalFeedUrl($user->fresh()),
            ]);
    }

    public function test_choosing_a_calendar_shows_its_feed(): void
    {
        $user = $this->member();
        $calendar = Calendar::factory()->createQuietly(['name' => 'Training']);
        $this->actingAs($user);

        Livewire::test(CalendarPage::class)
            ->mountAction('subscribeToCalendar')
            ->fillForm(['calendar' => (string) $calendar->getKey()])
            ->assertMountedActionModalSee(CalendarFeedService::calendarFeedUrl($user->fresh(), $calendar));
    }

    public function test_resetting_the_link_rotates_the_token(): void
    {
        $user = $this->member();
        $token = CalendarFeedService::tokenFor($user);
        $this->actingAs($user);

        Livewire::test(CalendarPage::class)
            ->callAction([TestAction::make('subscribeToCalendar'), TestAction::make('resetCalendarLink')])
            ->assertNotified('Your calendar link has been reset.');

        $this->assertNotSame($token, $user->fresh()->calendar_feed_token);
    }

    public function test_the_subscribe_action_is_hidden_from_members_who_cannot_view_events(): void
    {
        $this->actingAs(User::factory()->createQuietly());

        Livewire::test(CalendarPage::class)
            ->assertActionHidden('subscribeToCalendar');
    }

    private function member(): User
    {
        $user = User::factory()->createQuietly();
        $user->givePermissionTo(['view_any_event', 'view_event']);

        return $user;
    }
}
