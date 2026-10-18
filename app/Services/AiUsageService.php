<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\AiUsageLimitExceededException;
use App\Features\AiAssistant;
use App\Models\AiUsage;
use Laravel\Pennant\Feature;

class AiUsageService
{
    public static function isAvailable(): bool
    {
        return tenancy()->initialized
            && Feature::for(tenant())->active(AiAssistant::class)
            && static::remainingTokens() > 0;
    }

    public static function monthlyLimit(): int
    {
        return (int) config('ai.limits.monthly_tokens');
    }

    public static function tokensUsedThisMonth(): int
    {
        return (int) AiUsage::query()
            ->where('created_at', '>=', now()->startOfMonth())
            ->selectRaw('COALESCE(SUM(input_tokens + output_tokens), 0) as total')
            ->value('total');
    }

    public static function remainingTokens(): int
    {
        return max(0, static::monthlyLimit() - static::tokensUsedThisMonth());
    }

    /**
     * @throws AiUsageLimitExceededException
     */
    public static function ensureWithinLimit(): void
    {
        if (static::remainingTokens() <= 0) {
            throw new AiUsageLimitExceededException(static::monthlyLimit());
        }
    }
}
