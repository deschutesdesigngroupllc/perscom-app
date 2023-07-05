<?php

namespace Tests\Feature\Tenant\Observers;

use App\Notifications\System\DomainCreated;
use App\Notifications\System\DomainDeleted;
use App\Notifications\System\DomainUpdated;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Tenant\TenantTestCase;

class DomainObserverTest extends TenantTestCase
{
    public function test_new_domain_notification_sent()
    {
        Notification::fake();

        $this->tenant->domains()->create([
            'domain' => 'foo',
        ]);

        Notification::assertSentTo($this->tenant, DomainCreated::class);
    }

    public function test_updated_domain_notification_sent()
    {
        Notification::fake();

        $this->tenant->domain->update([
            'domain' => 'foo',
        ]);

        Notification::assertSentTo($this->tenant, DomainUpdated::class);
    }

    public function test_deleted_domain_notification_sent()
    {
        Notification::fake();

        $domain = $this->tenant->domains()->create([
            'domain' => 'foo',
        ]);
        $domain->delete();

        Notification::assertSentTo($this->tenant, DomainDeleted::class);
    }
}
