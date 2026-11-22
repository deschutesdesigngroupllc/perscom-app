<?php

declare(strict_types=1);

namespace Tests\Feature\Central\Jobs\System;

use App\Jobs\System\RemoveInactiveAccounts;
use App\Mail\System\DeleteAccountOneMonth as DeleteAccountOneMonthMail;
use App\Models\Tenant;
use App\Notifications\System\DeleteAccountOneMonth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\Feature\Central\CentralTestCase;

class RemoveInactiveAccountsTest extends CentralTestCase
{
    public function test_it_sends_a_one_month_warning_for_accounts_inactive_one_month(): void
    {
        Notification::fake();

        $tenant = Tenant::factory()->createQuietly();
        $tenant->forceFill(['created_at' => now()->subMonth()])->saveQuietly();

        (new RemoveInactiveAccounts)->handle();

        Notification::assertSentTo($tenant, DeleteAccountOneMonth::class);
    }

    public function test_it_does_not_notify_recently_active_accounts(): void
    {
        Notification::fake();

        $tenant = Tenant::factory()->createQuietly();
        $tenant->forceFill(['created_at' => now()->subDays(2)])->saveQuietly();

        (new RemoveInactiveAccounts)->handle();

        Notification::assertNothingSentTo($tenant);
        $this->assertDatabaseHas(Tenant::class, [
            'id' => $tenant->getKey(),
        ]);
    }

    public function test_it_never_deletes_or_warns_a_tenant_with_an_active_subscription(): void
    {
        Notification::fake();

        $tenant = Tenant::factory()->createQuietly();
        $tenant->forceFill(['created_at' => now()->subMonths(2), 'last_login_at' => now()->subMonths(2)])->saveQuietly();
        $tenant->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => Str::random(10),
            'stripe_status' => 'active',
            'stripe_price' => 'price_test_monthly',
            'quantity' => 1,
            'ends_at' => null,
        ]);

        (new RemoveInactiveAccounts)->handle();

        Notification::assertNothingSentTo($tenant);
        $this->assertDatabaseHas(Tenant::class, ['id' => $tenant->getKey()]);
    }

    public function test_deletion_warning_names_the_organization_and_offers_a_sign_in_link(): void
    {
        $tenant = Tenant::factory()->createQuietly(['name' => 'Bravo Company']);
        $tenant->forceFill(['created_at' => now()->subMonth(), 'last_login_at' => null])->saveQuietly();

        $html = new DeleteAccountOneMonthMail($tenant)->render();

        $this->assertStringContainsString('Bravo Company Will Be Deleted in 30 Days', $html);
        $this->assertStringContainsString('Sign In to Keep Bravo Company', $html);
        $this->assertStringContainsString(now()->addMonth()->toFormattedDateString(), $html);
    }

    public function test_it_uses_the_system_queue(): void
    {
        $this->assertSame('system', (new RemoveInactiveAccounts)->queue);
    }

    public function test_it_declares_a_backoff_schedule(): void
    {
        $this->assertSame([1, 5, 10], (new RemoveInactiveAccounts)->backoff());
    }
}
