<?php

namespace Tests\Feature\Tenant\Observers;

use App\Models\Tenant;
use App\Notifications\Admin\NewTenant;
use App\Notifications\Admin\TenantDeleted;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Tenant\TenantTestCase;

class TenantObserverTest extends TenantTestCase
{
    protected $fakeNotification = true;

    public function test_create_tenant_notification_sent()
    {
        Notification::assertSentTo($this->superAdmin, NewTenant::class);
    }

    public function test_delete_tenant_notification_sent()
    {
        $tenant = Tenant::factory()->create();
        $tenant->delete();

        Notification::assertSentTo($this->superAdmin, TenantDeleted::class);
    }
}
