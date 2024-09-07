<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PermissionSettings extends Settings
{
    public array $default_roles;

    public array $default_permissions;

    public static function group(): string
    {
        return 'permission';
    }
}
