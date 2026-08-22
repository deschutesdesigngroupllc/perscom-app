<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Listeners;

use App\Listeners\ProcessNotificationSent;
use App\Models\Message;
use App\Models\User;
use App\Notifications\Tenant\NewMessage;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Notifications\Notification;
use Tests\Feature\Tenant\TenantTestCase;

class ProcessNotificationSentTest extends TenantTestCase
{
    public function test_it_stamps_sent_at_on_the_message_for_a_new_message_notification(): void
    {
        $notifiable = User::factory()->createQuietly();
        $message = Message::factory()->createQuietly(['sent_at' => null]);

        $event = new NotificationSent($notifiable, new NewMessage($message), 'mail');

        new ProcessNotificationSent()->handle($event);

        $this->assertNotNull($message->fresh()->sent_at);
    }

    public function test_it_ignores_other_notifications(): void
    {
        $notifiable = User::factory()->createQuietly();
        $message = Message::factory()->createQuietly(['sent_at' => null]);

        $notification = new class extends Notification {};

        $event = new NotificationSent($notifiable, $notification, 'mail');

        new ProcessNotificationSent()->handle($event);

        $this->assertNull($message->fresh()->sent_at);
    }
}
