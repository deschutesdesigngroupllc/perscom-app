<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum ProductTerm: string implements HasLabel
{
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';

    public function getLabel(): ?string
    {
        return Str::of($this->value)->title()->toString();
    }
}
