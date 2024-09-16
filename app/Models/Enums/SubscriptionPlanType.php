<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum SubscriptionPlanType: string implements HasColor, HasLabel
{
    case BASIC = 'basic';
    case PRO = 'pro';
    case ENTERPRISE = 'enterprise';
    case NONE = 'no_plan';

    public function getColor(): string|array|null
    {
        return match ($this) {
            SubscriptionPlanType::BASIC => 'warning',
            SubscriptionPlanType::PRO => 'info',
            SubscriptionPlanType::ENTERPRISE => 'success',
            SubscriptionPlanType::NONE => 'gray'
        };
    }

    public function getLabel(): ?string
    {
        return Str::of($this->value)->replace('_', ' ')->title()->toString();
    }
}
