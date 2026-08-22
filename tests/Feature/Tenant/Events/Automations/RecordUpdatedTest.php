<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Events\Automations;

use App\Events\Automations\RecordUpdated;
use App\Models\Enums\AutomationTrigger;
use App\Models\User;
use Tests\Feature\Tenant\TenantTestCase;

class RecordUpdatedTest extends TenantTestCase
{
    public function test_it_exposes_the_subject_and_trigger_type(): void
    {
        $user = User::factory()->createQuietly();

        $event = new RecordUpdated($user, AutomationTrigger::USER_UPDATED);

        $this->assertSame($user, $event->getSubject());
        $this->assertSame(AutomationTrigger::USER_UPDATED->value, $event->getTriggerType());
    }

    public function test_it_retains_the_changed_attributes_passed_to_the_constructor(): void
    {
        $user = User::factory()->createQuietly();

        $changedAttributes = [
            'name' => ['old' => 'Old Name', 'new' => 'New Name'],
        ];

        $event = new RecordUpdated($user, AutomationTrigger::USER_UPDATED, $changedAttributes);

        $this->assertSame($changedAttributes, $event->getChangedAttributes());
    }

    public function test_it_builds_changed_attributes_from_a_dirty_model(): void
    {
        $user = User::factory()->createQuietly();
        $originalName = $user->name;

        // buildChangedAttributes relies on getOriginal(), which only reflects the
        // pre-save values while the model's "updated" event is firing (before
        // save() calls syncOriginal()). Capture it there, mirroring production usage.
        $changedAttributes = null;
        User::updated(function (User $model) use (&$changedAttributes): void {
            $changedAttributes = RecordUpdated::buildChangedAttributes($model);
        });

        $user->name = 'A Brand New Name';
        $user->save();

        $this->assertIsArray($changedAttributes);
        $this->assertArrayHasKey('name', $changedAttributes);
        $this->assertSame($originalName, $changedAttributes['name']['old']);
        $this->assertSame('A Brand New Name', $changedAttributes['name']['new']);
    }
}
