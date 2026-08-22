<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Events\Automations;

use App\Events\Automations\UserCreated;
use App\Models\Enums\AutomationTrigger;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\Feature\Tenant\TenantTestCase;

class UserCreatedTest extends TenantTestCase
{
    public function test_it_returns_the_user_created_trigger_type(): void
    {
        $user = User::factory()->createQuietly();

        $event = new UserCreated($user);

        $this->assertSame(AutomationTrigger::USER_CREATED->value, $event->getTriggerType());
        $this->assertSame('user.created', $event->getTriggerType());
    }

    public function test_it_exposes_the_subject_and_changed_attributes(): void
    {
        $user = User::factory()->createQuietly();
        $changes = ['name' => ['old' => 'Old', 'new' => 'New']];

        $event = new UserCreated($user, $changes);

        $this->assertTrue($user->is($event->getSubject()));
        $this->assertSame($changes, $event->getChangedAttributes());
    }

    public function test_it_captures_the_authenticated_causer(): void
    {
        $causer = User::factory()->createQuietly();
        Auth::login($causer);

        $subject = User::factory()->createQuietly();
        $event = new UserCreated($subject);

        $this->assertNotNull($event->getCauser());
        $this->assertTrue($causer->is($event->getCauser()));
    }

    public function test_it_builds_expression_context_for_the_user_subject(): void
    {
        $user = User::factory()->createQuietly();

        $context = (new UserCreated($user))->getExpressionContext();

        $this->assertSame(User::class, $context->modelType);
        $this->assertSame($user->getKey(), $context->modelId);
    }
}
