<?php

declare(strict_types=1);

namespace App\Contracts;

interface PremiumFeature
{
    public static function canSubscribe(): bool;

    public static function canUnsubscribe(): bool;

    public static function settingsForm(): array;

    public static function settingsKey(): string;
}
