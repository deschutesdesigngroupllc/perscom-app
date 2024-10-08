<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class UserSettings extends Settings
{
    public array $notifications;

    public array $timezone;

    public static function group(): string
    {
        return 'user';
    }
}
