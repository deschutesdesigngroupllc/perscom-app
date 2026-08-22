<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Events\Automations;

use App\Events\Automations\UserUpdated;
use App\Models\Enums\AutomationTrigger;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\Feature\Tenant\TenantTestCase;

class UserUpdatedTest extends TenantTestCase
{
    public function test_it_returns_the_user_updated_trigger_type(): void
    {
        $user = User::factory()->createQuietly();

        $event = new UserUpdated($user);

        $this->assertSame(AutomationTrigger::USER_UPDATED->value, $event->getTriggerType());
        $this->assertSame('user.updated', $event->getTriggerType());
    }

    public function test_it_exposes_the_subject_and_changed_attributes(): void
    {
        $user = User::factory()->createQuietly();
        $changes = ['email' => ['old' => 'a@example.com', 'new' => 'b@example.com']];

        $event = new UserUpdated($user, $changes);

        $this->assertTrue($user->is($event->getSubject()));
        $this->assertSame($changes, $event->getChangedAttributes());
    }

    public function test_it_builds_changed_attributes_from_a_dirty_model(): void
    {
        $user = User::factory()->createQuietly();
        $user->update(['name' => 'Renamed User']);

        $changed = UserUpdated::buildChangedAttributes($user);

        $this->assertNotNull($changed);
        $this->assertArrayHasKey('name', $changed);
        $this->assertSame('Renamed User', $changed['name']['new']);
    }

    public function test_it_captures_the_authenticated_causer(): void
    {
        $causer = User::factory()->createQuietly();
        Auth::login($causer);

        $subject = User::factory()->createQuietly();
        $event = new UserUpdated($subject);

        $this->assertNotNull($event->getCauser());
        $this->assertTrue($causer->is($event->getCauser()));
    }
}
