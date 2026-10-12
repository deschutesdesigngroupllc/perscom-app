<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Notifications\Tenant;

use App\Mail\Tenant\EventOccurrenceCancelledMail;
use App\Models\Enums\NotificationChannel;
use App\Models\Event;
use App\Models\EventOccurrence;
use App\Models\User;
use App\Notifications\Tenant\EventOccurrenceCancelled;
use App\Services\UserSettingsService;
use Illuminate\Support\Facades\Date;
use Tests\Feature\Tenant\TenantTestCase;

class EventOccurrenceCancelledTest extends TenantTestCase
{
    public function test_it_uses_in_app_and_email_channels_when_the_event_has_none_configured(): void
    {
        $user = User::factory()->createQuietly();
        $occurrence = $this->cancelledOccurrence(['notifications_channels' => null]);

        $this->assertEqualsCanonicalizing(['database', 'broadcast', 'mail'], new EventOccurrenceCancelled($occurrence)->via($user));
    }

    public function test_it_uses_the_channels_configured_on_the_event(): void
    {
        $user = User::factory()->createQuietly();
        $occurrence = $this->cancelledOccurrence(['notifications_channels' => [NotificationChannel::MAIL, NotificationChannel::DISCORD_PUBLIC]]);

        $this->assertSame(['mail'], new EventOccurrenceCancelled($occurrence)->via($user));
    }

    public function test_messages_show_the_date_in_the_members_timezone_with_the_reason(): void
    {
        $user = User::factory()->createQuietly();
        UserSettingsService::save('timezone', 'America/New_York', $user);
        $occurrence = $this->cancelledOccurrence();

        $notification = new EventOccurrenceCancelled($occurrence);
        $database = $notification->toDatabase($user);

        $this->assertSame('Event Cancelled - Weekly Ops', $database['title']);
        $this->assertStringContainsString('Thu, Jun 11, 2026 at 4:00 PM', $database['body']);
        $this->assertStringContainsString('Range closed.', $database['body']);

        $mail = $notification->toMail($user);

        $this->assertInstanceOf(EventOccurrenceCancelledMail::class, $mail);
        $mail->assertHasSubject('Cancelled: Weekly Ops on Thu, Jun 11, 2026 at 4:00 PM');
        $mail->assertSeeInHtml('Range closed.');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function cancelledOccurrence(array $attributes = []): EventOccurrence
    {
        $event = Event::factory()->createQuietly([
            'name' => 'Weekly Ops',
            'all_day' => false,
            'repeats' => true,
            'starts' => Date::parse('2026-06-04 20:00:00'),
            'ends' => Date::parse('2026-06-04 22:00:00'),
            ...$attributes,
        ]);

        return EventOccurrence::factory()->for($event)->cancelled()->createQuietly([
            'starts' => Date::parse('2026-06-11 20:00:00'),
            'ends' => Date::parse('2026-06-11 22:00:00'),
            'notes' => 'Range closed.',
        ]);
    }
}
