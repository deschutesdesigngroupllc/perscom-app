<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Events\Automations;

use App\Events\Automations\RecordCreated;
use App\Models\Enums\AutomationTrigger;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\Feature\Tenant\TenantTestCase;

class RecordCreatedTest extends TenantTestCase
{
    public function test_it_exposes_the_subject_and_trigger_type(): void
    {
        $user = User::factory()->createQuietly();

        $event = new RecordCreated($user, AutomationTrigger::USER_CREATED);

        $this->assertSame($user, $event->getSubject());
        $this->assertSame(AutomationTrigger::USER_CREATED->value, $event->getTriggerType());
        $this->assertNull($event->getChangedAttributes());
    }

    public function test_it_captures_the_authenticated_causer(): void
    {
        $causer = User::factory()->createQuietly();
        Auth::login($causer);

        $subject = User::factory()->createQuietly();

        $event = new RecordCreated($subject, AutomationTrigger::USER_CREATED);

        $this->assertSame($causer->getKey(), $event->getCauser()?->getKey());
    }
}
