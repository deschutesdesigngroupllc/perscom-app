<?php

namespace Tests\Feature\Tenant\Observers;

use App\Jobs\SendBulkMail;
use App\Models\Mail;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Tests\Feature\Tenant\TenantTestCase;

class MailObserverTest extends TenantTestCase
{
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
