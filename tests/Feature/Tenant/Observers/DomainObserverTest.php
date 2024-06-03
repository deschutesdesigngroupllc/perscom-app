<?php

namespace Tests\Feature\Tenant\Observers;

use App\Models\Tenant;
use App\Notifications\System\DomainCreated;
use App\Notifications\System\DomainDeleted;
use App\Notifications\System\DomainUpdated;
use Exception;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Tenant\TenantTestCase;

class DomainObserverTest extends TenantTestCase
{
    /**
     * @throws Exception
     */
    public function test_new_domain_notification_sent()
    {
        Notification::fake();

        $tenant = Tenant::factory()->createQuietly();
        $tenant->domains()->create([
            'domain' => $this->faker()->unique()->domainWord,
        ]);

        Notification::assertSentTo($tenant, DomainCreated::class);
    }

    /**
     * @throws Exception
     */
    public function test_updated_domain_notification_sent()
    {
        Notification::fake();

        $tenant = Tenant::factory()->createQuietly();
        $domain = $tenant->domains()->createQuietly([
            'domain' => $this->faker()->unique()->domainWord,
        ]);
        $domain->update([
            'domain' => $this->faker()->unique()->domainWord,
        ]);

        Notification::assertSentTo($tenant, DomainUpdated::class);
    }

    /**
     * @throws Exception
     */
    public function test_deleted_domain_notification_sent()
    {
        Notification::fake();

        $tenant = Tenant::factory()->createQuietly();
        $tenant->domains()->createQuietly([
            'domain' => $this->faker()->unique()->domainWord,
        ]);

        $domain = $tenant->domains()->createQuietly([
            'domain' => $this->faker()->unique()->domainWord,
            'is_custom_subdomain' => true,
        ]);
        $domain->delete();

        Notification::assertSentTo($tenant, DomainDeleted::class);
    }
}
