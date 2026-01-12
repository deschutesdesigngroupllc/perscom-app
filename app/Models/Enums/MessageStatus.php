<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

enum MessageStatus: string implements HasColor, HasLabel
{
    case Pending = 'pending';
    case Sent = 'sent';

    public function getLabel(): string|Htmlable|null
    {
        return Str::of($this->value)
            ->title()
            ->toString();
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            MessageStatus::Pending => 'info',
            MessageStatus::Sent => 'success',
        };
    }
}
