<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Services;

use App\Exceptions\AiUsageLimitExceededException;
use App\Models\AiUsage;
use App\Services\AiUsageService;
use Tests\Feature\Tenant\TenantTestCase;

class AiUsageServiceTest extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'ai.default' => 'anthropic',
            'ai.providers.anthropic.key' => 'test-key',
            'ai.limits.monthly_tokens' => 1000,
        ]);
    }

    public function test_it_sums_only_this_months_tokens(): void
    {
        AiUsage::factory()->createQuietly(['input_tokens' => 100, 'output_tokens' => 50]);
        AiUsage::factory()->createQuietly(['input_tokens' => 500, 'output_tokens' => 500, 'created_at' => now()->subMonth()]);

        $this->assertSame(150, AiUsageService::tokensUsedThisMonth());
        $this->assertSame(850, AiUsageService::remainingTokens());
    }

    public function test_it_throws_when_the_monthly_limit_is_reached(): void
    {
        AiUsage::factory()->createQuietly(['input_tokens' => 600, 'output_tokens' => 400]);

        $this->expectException(AiUsageLimitExceededException::class);

        AiUsageService::ensureWithinLimit();
    }

    public function test_it_is_available_when_a_provider_key_is_configured_and_within_limit(): void
    {
        $this->assertTrue(AiUsageService::isAvailable());
    }

    public function test_it_is_unavailable_without_a_provider_key(): void
    {
        config(['ai.providers.anthropic.key' => null]);

        $this->assertFalse(AiUsageService::isAvailable());
    }

    public function test_it_is_unavailable_once_the_limit_is_reached(): void
    {
        AiUsage::factory()->createQuietly(['input_tokens' => 1000, 'output_tokens' => 0]);

        $this->assertFalse(AiUsageService::isAvailable());
    }
}
