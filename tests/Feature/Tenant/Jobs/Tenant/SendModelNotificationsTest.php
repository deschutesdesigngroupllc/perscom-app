<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Jobs\Tenant;

use App\Jobs\Tenant\SendModelNotifications;
use App\Models\Announcement;
use App\Models\Enums\NotificationChannel;
use App\Models\ModelNotification;
use App\Models\User;
use App\Notifications\Tenant\NewModelNotification;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Tenant\TenantTestCase;

class SendModelNotificationsTest extends TenantTestCase
{
    public function test_it_notifies_the_configured_recipient_for_the_matching_event(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $announcement = Announcement::factory()->createQuietly();

        $announcement->modelNotifications()->create(
            ModelNotification::forUser($user, 'test.event', 'Subject', 'Message', [NotificationChannel::MAIL])
        );

        new SendModelNotifications($announcement, 'test.event')->handle();

        Notification::assertSentTo($user, NewModelNotification::class);
    }

    public function test_it_does_not_notify_when_no_notification_matches_the_event(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $announcement = Announcement::factory()->createQuietly();

        $announcement->modelNotifications()->create(
            ModelNotification::forUser($user, 'other.event', 'Subject', 'Message', [NotificationChannel::MAIL])
        );

        new SendModelNotifications($announcement, 'test.event')->handle();

        Notification::assertNothingSent();
    }

    public function test_it_no_ops_when_the_model_has_no_notifications(): void
    {
        Notification::fake();

        $announcement = Announcement::factory()->createQuietly();

        new SendModelNotifications($announcement, 'test.event')->handle();

        Notification::assertNothingSent();
    }
}
