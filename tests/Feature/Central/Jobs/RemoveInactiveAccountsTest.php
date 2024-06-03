<?php

namespace Tests\Feature\Central\Jobs;

use App\Jobs\RemoveInactiveAccounts as RemoveInactiveAccountsJob;
use App\Models\Tenant;
use App\Notifications\System\DeleteAccount;
use App\Notifications\System\DeleteAccountOneMonth;
use App\Notifications\System\DeleteAccountOneWeek;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Central\CentralTestCase;

class RemoveInactiveAccountsTest extends CentralTestCase
{
    public function test_five_months_inactive_notification_sent()
    {
        Notification::fake();

        $tenant = Tenant::factory()->state([
            'last_login_at' => now()->subMonths(5),
        ])->create();

        RemoveInactiveAccountsJob::dispatchSync();

        Notification::assertSentTo($tenant, DeleteAccountOneMonth::class, function ($notification, $channels) {
            return true;
        });
    }

    public function test_one_week_inactive_notification_sent()
    {
        Notification::fake();

        $tenant = Tenant::factory()->state([
            'last_login_at' => now()->subMonths(6)->addWeek(),
        ])->create();

        RemoveInactiveAccountsJob::dispatchSync();

        Notification::assertSentTo($tenant, DeleteAccountOneWeek::class, function ($notification, $channels) {
            return true;
        });
    }

    public function test_six_months_inactive_notification_sent()
    {
        Notification::fake();

        $tenant = Tenant::factory()->state([
            'last_login_at' => now()->subMonths(6),
        ])->create();

        RemoveInactiveAccountsJob::dispatchSync();

        Notification::assertSentTo($tenant, DeleteAccount::class, function ($notification, $channels) {
            return true;
        });
    }

    public function test_six_months_inactive_tenant_deleted()
    {
        $tenant = Tenant::factory()->state([
            'last_login_at' => now()->subMonths(6),
        ])->create();

        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->getKey(),
        ]);

        RemoveInactiveAccountsJob::dispatchSync();

        $this->assertSoftDeleted('tenants', [
            'id' => $tenant->getKey(),
        ]);
    }
}
