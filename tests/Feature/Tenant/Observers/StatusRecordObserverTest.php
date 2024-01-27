<?php

namespace Tests\Feature\Tenant\Observers;

use App\Models\Status;
use App\Models\User;
use Tests\Feature\Tenant\TenantTestCase;

class StatusRecordObserverTest extends TenantTestCase
{
    public function test_create_status_record_assigns_user_status(): void
    {
        $status = Status::factory()->create();
        $user = User::factory()->create();

        $user->statuses()->attach($status);

        $this->assertSame($status->getKey(), $user->fresh()->status->getKey());
    }
}
