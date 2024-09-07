<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class IntegrationSettings extends Settings
{
    public string $single_sign_on_key;

    public static function group(): string
    {
        return 'integration';
    }
}
