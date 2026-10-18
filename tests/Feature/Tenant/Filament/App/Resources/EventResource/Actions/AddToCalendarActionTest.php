<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Resources\EventResource\Actions;

use App\Filament\App\Resources\EventResource\Pages\ViewEvent;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Date;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class AddToCalendarActionTest extends TenantTestCase
{
    public function test_the_event_page_offers_download_and_google_calendar_links(): void
    {
        $user = User::factory()->createQuietly();
        $user->givePermissionTo(['view_any_event', 'view_event']);
        $this->actingAs($user);

        $event = Event::factory()->createQuietly([
            'name' => 'Range Day',
            'all_day' => false,
            'repeats' => false,
            'starts' => Date::parse('2030-06-04 20:00:00'),
            'ends' => Date::parse('2030-06-04 21:00:00'),
        ]);

        Livewire::test(ViewEvent::class, ['record' => $event->getKey()])
            ->assertSee('Add to Calendar')
            ->assertActionVisible('downloadIcs')
            ->assertActionHasUrl('downloadIcs', route('app.events.calendar', ['event' => $event->getKey(), 'occurrence' => $event->starts->getTimestamp()]))
            ->assertActionVisible('googleCalendar')
            ->assertActionShouldOpenUrlInNewTab('googleCalendar');
    }
}
