<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum CredentialType: string implements HasLabel
{
    case CERTIFICATION = 'certification';
    case LICENSE = 'license';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return Str::title($this->value);
    }
}
