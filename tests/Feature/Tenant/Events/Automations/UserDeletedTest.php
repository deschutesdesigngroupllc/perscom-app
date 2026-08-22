<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Events\Automations;

use App\Events\Automations\UserDeleted;
use App\Models\Enums\AutomationTrigger;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\Feature\Tenant\TenantTestCase;

class UserDeletedTest extends TenantTestCase
{
    public function test_it_returns_the_user_deleted_trigger_type(): void
    {
        $user = User::factory()->createQuietly();

        $event = new UserDeleted($user);

        $this->assertSame(AutomationTrigger::USER_DELETED->value, $event->getTriggerType());
        $this->assertSame('user.deleted', $event->getTriggerType());
    }

    public function test_it_exposes_the_deleted_subject(): void
    {
        $user = User::factory()->createQuietly();

        $event = new UserDeleted($user);

        $this->assertTrue($user->is($event->getSubject()));
        $this->assertNull($event->getChangedAttributes());
    }

    public function test_it_captures_the_authenticated_causer(): void
    {
        $causer = User::factory()->createQuietly();
        Auth::login($causer);

        $subject = User::factory()->createQuietly();
        $event = new UserDeleted($subject);

        $this->assertNotNull($event->getCauser());
        $this->assertTrue($causer->is($event->getCauser()));
    }

    public function test_it_builds_expression_context_for_the_user_subject(): void
    {
        $user = User::factory()->createQuietly();

        $context = (new UserDeleted($user))->getExpressionContext();

        $this->assertSame(User::class, $context->modelType);
        $this->assertSame($user->getKey(), $context->modelId);
    }
}
