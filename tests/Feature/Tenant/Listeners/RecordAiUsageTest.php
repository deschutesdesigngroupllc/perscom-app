<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Listeners;

use App\Models\AiUsage;
use App\Models\User;
use Laravel\Ai\AnonymousAgent;
use Tests\Feature\Tenant\TenantTestCase;

class RecordAiUsageTest extends TenantTestCase
{
    public function test_it_records_token_usage_for_the_acting_user_when_an_agent_is_prompted(): void
    {
        $user = User::factory()->createQuietly();
        $this->actingAs($user);

        AnonymousAgent::fake(['Hello there.']);

        new AnonymousAgent('You are helpful.', [], [])->prompt('Hi');

        $usage = AiUsage::query()->sole();

        $this->assertSame($user->getKey(), $usage->user_id);
        $this->assertSame(AnonymousAgent::class, $usage->agent);
    }
}
