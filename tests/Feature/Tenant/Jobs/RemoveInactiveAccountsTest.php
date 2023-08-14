<?php

namespace Tests\Feature\Tenant\Jobs;

use App\Jobs\RemoveInactiveAccounts as RemoveInactiveAccountsJob;
use App\Models\Tenant;
use App\Notifications\System\DeleteAccountOneMonth;
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
}
