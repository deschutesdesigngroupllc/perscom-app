<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum SubscriptionStatus: string implements HasColor, HasLabel
{
    case ACTIVE = 'active';
    case INCOMPLETE = 'incomplete';
    case INCOMPLETE_EXPIRED = 'incomplete_expired';
    case TRIALING = 'trialing';
    case PAST_DUE = 'past_due';
    case CANCELED = 'canceled';
    case UNPAID = 'unpaid';
    case NONE = 'no_subscription';

    public function getColor(): string|array|null
    {
        return match ($this) {
            SubscriptionStatus::ACTIVE => 'success',
            SubscriptionStatus::INCOMPLETE, SubscriptionStatus::INCOMPLETE_EXPIRED => 'warning',
            SubscriptionStatus::TRIALING => 'info',
            SubscriptionStatus::PAST_DUE => 'danger',
            SubscriptionStatus::CANCELED, SubscriptionStatus::UNPAID, SubscriptionStatus::NONE => 'gray',
        };
    }

    public function getLabel(): ?string
    {
        return Str::of($this->value)->replace('_', ' ')->title()->toString();
    }
}
