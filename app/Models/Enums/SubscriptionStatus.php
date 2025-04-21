<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum SubscriptionStatus: string implements HasColor, HasLabel
{
    case Active = 'active';
    case Incomplete = 'incomplete';
    case IncompleteExpired = 'incomplete_expired';
    case Trialing = 'trialing';
    case PastDue = 'past_due';
    case Canceled = 'canceled';
    case Unpaid = 'unpaid';
    case None = 'no_subscription';

    public function getColor(): string|array|null
    {
        return match ($this) {
            SubscriptionStatus::Active => 'success',
            SubscriptionStatus::Incomplete, SubscriptionStatus::IncompleteExpired => 'warning',
            SubscriptionStatus::Trialing => 'info',
            SubscriptionStatus::PastDue => 'danger',
            SubscriptionStatus::Canceled, SubscriptionStatus::Unpaid, SubscriptionStatus::None => 'gray',
        };
    }

    public function getLabel(): ?string
    {
        return Str::of($this->value)->replace('_', ' ')->title()->toString();
    }
}
