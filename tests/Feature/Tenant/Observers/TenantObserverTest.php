<?php

namespace Tests\Feature\Tenant\Observers;

use App\Notifications\Admin\NewSubscription;
use App\Notifications\Admin\NewTenant;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Tenant\TenantTestCase;

class TenantObserverTest extends TenantTestCase
{
    protected $fakeNotification = true;

    public function test_new_tenant_notification_sent()
    {
        Notification::assertSentTo($this->admin, NewTenant::class, function ($notification, $channels) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($this->admin);
            $mail->assertTo($this->admin->email);

            return true;
        });
    }

    public function test_new_subscription_notification_sent()
    {
        $this->withSubscription();

        Notification::assertSentTo($this->admin, NewSubscription::class, function ($notification, $channels) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($this->admin);
            $mail->assertTo($this->admin->email);

            return true;
        });
    }
}
