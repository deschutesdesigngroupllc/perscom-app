<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum InviteStatus: string implements HasColor, HasLabel
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case EXPIRED = 'expired';
    case REVOKED = 'revoked';

    public function getLabel(): string
    {
        return Str::title($this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            InviteStatus::PENDING => 'warning',
            InviteStatus::ACCEPTED => 'success',
            InviteStatus::EXPIRED => 'gray',
            InviteStatus::REVOKED => 'danger',
        };
    }
}
