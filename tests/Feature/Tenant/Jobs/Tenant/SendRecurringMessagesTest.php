<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Jobs\Tenant;

use App\Jobs\Tenant\SendRecurringMessages;
use App\Models\Enums\NotificationChannel;
use App\Models\Enums\ScheduleEndType;
use App\Models\Enums\ScheduleFrequency;
use App\Models\Message;
use App\Models\User;
use App\Notifications\Tenant\NewMessage;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Tenant\TenantTestCase;

class SendRecurringMessagesTest extends TenantTestCase
{
    public function test_it_sends_recurring_messages_scheduled_within_the_next_day(): void
    {
        Notification::fake();

        $user = User::factory()->createQuietly();

        $message = Message::factory()->createQuietly([
            'recipients' => null,
            'channels' => [NotificationChannel::MAIL],
            'repeats' => true,
        ]);

        $message->schedule()->create([
            'start' => now()->addHour(),
            'duration' => 1,
            'frequency' => ScheduleFrequency::DAILY,
            'interval' => 1,
            'end_type' => ScheduleEndType::NEVER,
        ]);

        (new SendRecurringMessages)->work();

        Notification::assertSentTo($user, NewMessage::class);
        Notification::assertSentTimes(NewMessage::class, 1);
    }

    public function test_it_ignores_non_repeating_messages(): void
    {
        Notification::fake();

        User::factory()->createQuietly();

        Message::factory()->createQuietly([
            'recipients' => null,
            'channels' => [NotificationChannel::MAIL],
            'repeats' => false,
        ]);

        (new SendRecurringMessages)->work();

        Notification::assertNothingSent();
    }
}
