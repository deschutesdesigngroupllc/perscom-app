<?php

namespace Tests\Feature\Central\Observers;

use App\Models\Tenant;
use App\Notifications\Admin\NewSubscription;
use App\Notifications\Admin\NewTenant;
use App\Notifications\Admin\TenantDeleted;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Stancl\Tenancy\Events\TenantCreated;
use Tests\Feature\Central\CentralTestCase;

class TenantObserverTest extends CentralTestCase
{
    public function test_new_tenant_notification_sent()
    {
        Event::fake([TenantCreated::class]);
        Notification::fake();

        Tenant::factory()->create();

        Event::assertDispatched(TenantCreated::class);
        Notification::assertSentTo($this->admin, NewTenant::class, function ($notification, $channels) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($this->admin);
            $mail->assertTo($this->admin->email);

            return true;
        });
    }

    public function test_new_subscription_notification_sent()
    {
        Notification::fake();

        $tenant = Tenant::factory()->createQuietly();
        $tenant->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => Str::random(10),
            'stripe_status' => 'active',
            'stripe_price' => env('STRIPE_PRODUCT_BASIC_MONTH'),
            'quantity' => 1,
            'trial_ends_at' => now()->addWeek(),
            'ends_at' => null,
        ]);

        Notification::assertSentTo($this->admin, NewSubscription::class, function ($notification, $channels) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($this->admin);
            $mail->assertTo($this->admin->email);

            return true;
        });
    }

    public function test_tenant_deleted_notification_sent()
    {
        Notification::fake();

        $tenant = Tenant::factory()->create();
        $tenant->delete();

        Notification::assertSentTo($this->admin, TenantDeleted::class, function ($notification, $channels) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($this->admin);
            $mail->assertTo($this->admin->email);

            $nova = $notification->toNova();
            $this->assertSame('A tenant has been deleted.', $nova->message);

            return true;
        });
    }
}
