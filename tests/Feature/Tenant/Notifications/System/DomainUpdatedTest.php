<?php

namespace Tests\Feature\Tenant\Notifications\System;

use App\Notifications\System\DomainUpdated;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Tenant\TenantTestCase;

class DomainUpdatedTest extends TenantTestCase
{
    public function test_domain_updated_notification_is_sent()
    {
        Notification::fake();

        $domain = $this->tenant->domains()->create([
            'is_custom_subdomain' => true,
            'domain' => $this->faker->domainWord,
        ]);
        $domain->update([
            'domain' => $this->faker->domainWord,
        ]);

        Notification::assertSentTo($this->tenant, DomainUpdated::class);
    }
}
