<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Actions\Messages;

use App\Actions\Messages\SendMessage;
use App\Models\Enums\NotificationChannel;
use App\Models\Message;
use App\Models\User;
use App\Notifications\Tenant\NewMessage;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Tenant\TenantTestCase;

class SendMessageTest extends TenantTestCase
{
    public function test_it_sends_the_notification_to_all_users_when_no_recipients_are_set(): void
    {
        Notification::fake();

        $users = User::factory()->count(2)->createQuietly();

        $message = Message::factory()->createQuietly([
            'channels' => [NotificationChannel::DATABASE],
            'recipients' => null,
        ]);

        SendMessage::handle($message);

        foreach ($users as $user) {
            Notification::assertSentTo($user, NewMessage::class);
        }
    }

    public function test_it_does_not_send_when_the_message_has_no_channels(): void
    {
        Notification::fake();

        User::factory()->createQuietly();

        $message = Message::factory()->createQuietly([
            'channels' => null,
            'recipients' => null,
        ]);

        SendMessage::handle($message);

        Notification::assertNothingSent();
    }

    public function test_it_also_dispatches_to_the_discord_public_channel_when_selected(): void
    {
        Notification::fake();

        User::factory()->createQuietly();

        $message = Message::factory()->createQuietly([
            'channels' => [NotificationChannel::DISCORD_PUBLIC],
            'recipients' => null,
        ]);

        SendMessage::handle($message);

        Notification::assertSentTo(User::first(), NewMessage::class);
    }

    public function test_can_send_notification_returns_false_without_channels(): void
    {
        $message = Message::factory()->createQuietly([
            'channels' => null,
        ]);

        $this->assertFalse(SendMessage::canSendNotification($message));
    }

    public function test_can_send_notification_returns_true_with_channels(): void
    {
        $message = Message::factory()->createQuietly([
            'channels' => [NotificationChannel::DATABASE],
        ]);

        $this->assertTrue(SendMessage::canSendNotification($message));
    }
}
