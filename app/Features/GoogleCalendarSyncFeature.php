<?php

declare(strict_types=1);

namespace App\Features;

use App\Contracts\PremiumFeature;

class GoogleCalendarSyncFeature extends BaseFeature implements PremiumFeature
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
        return 'google_calendar_sync';
    }

    public function resolve(?string $scope): bool
    {
        return false;
    }
}
