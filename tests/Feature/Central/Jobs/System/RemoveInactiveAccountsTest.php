<?php

declare(strict_types=1);

namespace Tests\Feature\Central\Jobs\System;

use App\Jobs\System\RemoveInactiveAccounts;
use App\Models\Tenant;
use App\Notifications\System\DeleteAccountOneMonth;
use Illuminate\Support\Facades\Notification;
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

    public function test_it_uses_the_system_queue(): void
    {
        $this->assertSame('system', (new RemoveInactiveAccounts)->queue);
    }

    public function test_it_declares_a_backoff_schedule(): void
    {
        $this->assertSame([1, 5, 10], (new RemoveInactiveAccounts)->backoff());
    }
}
