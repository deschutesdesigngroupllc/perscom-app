<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AutomationLogStatus: string implements HasColor, HasLabel
{
    case PENDING = 'pending';
    case CONDITION_FAILED = 'condition_failed';
    case EXECUTED = 'executed';
    case FAILED = 'failed';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::CONDITION_FAILED => 'Condition Failed',
            self::EXECUTED => 'Executed',
            self::FAILED => 'Failed',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PENDING => 'info',
            self::CONDITION_FAILED => 'warning',
            self::EXECUTED => 'success',
            self::FAILED => 'danger',
        };
    }
}
