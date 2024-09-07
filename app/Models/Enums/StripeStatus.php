<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum StripeStatus: string implements HasColor, HasLabel
{
    case TRIALING = 'trialing';
    case ACTIVE = 'active';
    case INCOMPLETE = 'incomplete';
    case INCOMPLETE_EXPIRED = 'incomplete_expired';
    case PAST_DUE = 'past_due';
    case CANCELED = 'canceled';
    case UNPAID = 'unpaid';
    case PAUSED = 'paused';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::TRIALING => 'info',
            self::ACTIVE => 'success',
            self::INCOMPLETE => 'warning',
            self::INCOMPLETE_EXPIRED, self::PAST_DUE, self::CANCELED => 'danger',
            self::UNPAID => 'red',
            self::PAUSED => 'gray',
        };
    }

    public function getLabel(): ?string
    {
        return Str::of($this->value)->replace('_', ' ')->title()->toString();
    }
}
