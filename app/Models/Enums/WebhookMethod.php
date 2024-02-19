<?php

namespace App\Models\Enums;

use Illuminate\Support\Str;

enum WebhookMethod: string
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
