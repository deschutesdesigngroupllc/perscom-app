<?php

declare(strict_types=1);

namespace Tests\Feature\Central\Observers;

use App\Models\Tenant;
use App\Notifications\Admin\NewSubscription;
use App\Notifications\Admin\TenantCreated;
use App\Notifications\Admin\TenantDeleted;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Stancl\Tenancy\Events\TenantCreated as BaseTenantCreated;
use Stancl\Tenancy\Events\TenantDeleted as BaseTenantDeleted;
use Tests\Feature\Central\CentralTestCase;

class TenantObserverTest extends CentralTestCase
{
    public function test_new_tenant_notification_sent(): void
    {
        Event::fake([BaseTenantCreated::class]);
        Notification::fake();

        Tenant::factory()->create();

        Event::assertDispatched(BaseTenantCreated::class);
        Notification::assertSentTo($this->admin, TenantCreated::class, function ($notification, iterable $channels): true {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($this->admin);
            $mail->assertTo($this->admin->email);

            return true;
        });
    }

    public function test_new_subscription_notification_sent(): void
    {
        Notification::fake();

        $tenant = Tenant::factory()->createQuietly();
        $tenant->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => Str::random(10),
            'stripe_status' => 'active',
            'stripe_price' => env('STRIPE_PRODUCT_MONTH'),
            'quantity' => 1,
            'trial_ends_at' => now()->addWeek(),
            'ends_at' => null,
        ]);

        Notification::assertSentTo($this->admin, NewSubscription::class, function ($notification, iterable $channels): true {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($this->admin);
            $mail->assertTo($this->admin->email);

            return true;
        });
    }

    public function test_tenant_deleted_notification_sent(): void
    {
        Event::fake([BaseTenantDeleted::class]);
        Notification::fake();

        $tenant = Tenant::factory()->create();
        $tenant->delete();

        Event::assertDispatched(BaseTenantDeleted::class);
        Notification::assertSentTo($this->admin, TenantDeleted::class, function ($notification, iterable $channels): true {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($this->admin);
            $mail->assertTo($this->admin->email);

            return true;
        });
    }
}
