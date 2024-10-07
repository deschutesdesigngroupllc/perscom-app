<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class FeatureSettings extends Settings
{
    public array $advanced_notifications;

    public static function group(): string
    {
        return 'feature';
    }
}
