<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Observers;

use App\Jobs\SendBulkMail;
use App\Models\Mail;
use App\Models\User;
use App\Notifications\Tenant\NewMail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\Feature\Tenant\TenantTestCase;

class MailObserverTest extends TenantTestCase
{
    public function test_tenant_mail_notification_sent()
    {
        Notification::fake();

        $users = User::factory()->count(5)->create();

        $mail = Mail::factory()->state([
            'recipients' => $users->pluck('id')->toArray(),
        ])->create();

        SendBulkMail::dispatch($users, $mail);

        Notification::assertSentTo($users, NewMail::class, function (NewMail $notification, $channels, $user) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($user);
            $mail->assertTo($user->email);

            return true;
        });
    }

    public function test_tenant_mail_is_queued()
    {
        Queue::fake();

        $users = User::factory()->count(5)->create();

        Mail::factory()->state([
            'recipients' => $users->pluck('id'),
        ])->create();

        Queue::assertPushed(SendBulkMail::class);
    }
}
