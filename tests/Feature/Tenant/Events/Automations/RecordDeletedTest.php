<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Events\Automations;

use App\Events\Automations\RecordDeleted;
use App\Models\Enums\AutomationTrigger;
use App\Models\User;
use Tests\Feature\Tenant\TenantTestCase;

class RecordDeletedTest extends TenantTestCase
{
    public function test_it_exposes_the_subject_and_trigger_type(): void
    {
        $user = User::factory()->createQuietly();

        $event = new RecordDeleted($user, AutomationTrigger::USER_DELETED);

        $this->assertSame($user, $event->getSubject());
        $this->assertSame(AutomationTrigger::USER_DELETED->value, $event->getTriggerType());
        $this->assertNull($event->getChangedAttributes());
    }
}
