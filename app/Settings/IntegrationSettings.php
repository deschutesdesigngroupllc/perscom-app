<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class IntegrationSettings extends Settings
{
    public string $single_sign_on_key;

    public ?array $discord_settings = null;

    public ?array $sms_settings = null;

    public static function group(): string
    {
        return 'integration';
    }

    public static function encrypted(): array
    {
        return [
            'single_sign_on_key',
        ];
    }
}
