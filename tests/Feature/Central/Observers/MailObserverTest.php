<?php

namespace Tests\Feature\Central\Observers;

use App\Jobs\SendBulkMail;
use App\Models\Mail;
use App\Models\Tenant;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Queue;
use Tests\Feature\Central\CentralTestCase;

class MailObserverTest extends CentralTestCase
{
    public function test_admin_mail_is_queued()
    {
        Queue::fake();

        $tenant = Tenant::factory()->create();

        Mail::factory()->state([
            'recipients' => Arr::wrap($tenant->getKey()),
        ])->create();

        Queue::assertPushed(SendBulkMail::class);
    }
}
