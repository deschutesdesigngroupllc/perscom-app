<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum InviteType: string implements HasColor, HasDescription, HasLabel
{
    case CREATE = 'create';
    case CLAIM = 'claim';

    public function getLabel(): string
    {
        return match ($this) {
            InviteType::CREATE => 'New Profile',
            InviteType::CLAIM => 'Claim Profile',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            InviteType::CREATE => 'The recipient will create a brand new profile.',
            InviteType::CLAIM => 'The recipient will take ownership of an existing profile.',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            InviteType::CREATE => 'info',
            InviteType::CLAIM => 'primary',
        };
    }
}
