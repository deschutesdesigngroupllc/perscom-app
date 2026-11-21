<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class RegistrationSettings extends Settings
{
    public bool $enabled;

    public bool $admin_approval_required;

    /**
     * Whether people can create their own account, by email or through a social provider.
     */
    public static function isOpen(): bool
    {
        /** @var RegistrationSettings $settings */
        $settings = resolve(RegistrationSettings::class);

        return $settings->enabled;
    }

    public static function group(): string
    {
        return 'registration';
    }
}
