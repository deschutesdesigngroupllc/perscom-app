<?php

declare(strict_types=1);

namespace App\Features;

use App\Contracts\PremiumFeature;

class EnhancedAIFeature extends BaseFeature implements PremiumFeature
{
    public static function canSubscribe(): bool
    {
        return false;
    }

    public static function canUnsubscribe(): bool
    {
        return false;
    }

    public static function settingsForm(): array
    {
        return [];
    }

    public static function settingsKey(): string
    {
        return 'enhanced_ai';
    }

    public static function settingsIcon(): string
    {
        return 'heroicon-o-cube-transparent';
    }

    public function resolve(?string $scope): bool
    {
        return false;
    }
}
