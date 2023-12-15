<?php

namespace Tests\Feature\Tenant\Observers;

use App\Models\Status;
use Tests\Feature\Tenant\TenantTestCase;

class StatusRecordObserverTest extends TenantTestCase
{
    public function test_create_status_record_assigns_user_status(): void
    {
        $status = Status::factory()->create();

        $this->user->statuses()->attach($status);

        $user = $this->user->fresh();

        $this->assertSame($status->getKey(), $user->status->getKey());
    }
}
