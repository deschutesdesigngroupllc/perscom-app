<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum CredentialType: string implements HasLabel
{
    case Certification = 'certification';
    case License = 'license';
    case Other = 'other';

    public function getLabel(): string
    {
        return Str::title($this->value);
    }
}
