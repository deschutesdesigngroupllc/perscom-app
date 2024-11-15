<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class NotificationSettings extends Settings
{
    public ?array $assignment_records;

    public ?array $award_records;

    public ?array $combat_records;

    public ?array $qualification_records;

    public ?array $rank_records;

    public ?array $service_records;

    public static function group(): string
    {
        return 'notifications';
    }
}
