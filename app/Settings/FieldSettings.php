<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class FieldSettings extends Settings
{
    public ?array $assignment_records = null;

    public ?array $award_records = null;

    public ?array $combat_records = null;

    public ?array $qualification_records = null;

    public ?array $rank_records = null;

    public ?array $service_records = null;

    public ?array $training_records = null;

    public ?array $users = null;

    public static function group(): string
    {
        return 'fields';
    }
}
