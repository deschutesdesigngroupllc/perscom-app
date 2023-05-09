<?php

namespace App\Models\Enums;

enum WebhookMethod: string
{
    case GET = 'get';
    case POST = 'post';
}
