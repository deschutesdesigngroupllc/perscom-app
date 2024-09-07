<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class RegistrationSettings extends Settings
{
    public bool $enabled;

    public bool $admin_approval_required;

    public static function group(): string
    {
        return 'registration';
    }
}
