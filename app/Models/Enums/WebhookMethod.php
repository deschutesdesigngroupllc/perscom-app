<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum WebhookMethod: string implements HasColor, HasLabel
{
    case GET = 'get';
    case POST = 'post';

    public function getLabel(): string
    {
        return Str::upper($this->value);
    }

    public function getColor(): string
    {
        return 'info';
    }
}
