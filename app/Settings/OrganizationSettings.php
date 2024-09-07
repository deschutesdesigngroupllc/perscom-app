<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class OrganizationSettings extends Settings
{
    public string $name;

    public string $email;

    public string $timezone;

    public static function group(): string
    {
        return 'organization';
    }
}
