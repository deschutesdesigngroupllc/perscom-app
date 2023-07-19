<?php

namespace Tests\Feature\Tenant\Observers;

use App\Models\Tenant;
use App\Notifications\Admin\NewSubscription;
use App\Notifications\Admin\NewTenant;
use App\Notifications\Admin\TenantDeleted;
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

    public function test_tenant_deleted_notification_sent()
    {
        $tenant = Tenant::factory()->create();
        $tenant->delete();

        Notification::assertSentTo($this->admin, TenantDeleted::class, function ($notification, $channels) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($this->admin);
            $mail->assertTo($this->admin->email);

            return true;
        });
    }
}
