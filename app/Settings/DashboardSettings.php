<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class DashboardSettings extends Settings
{
    public string $title;

    public ?string $subtitle;

    public ?string $subdomain;

    public ?string $domain;

    public int $cover_photo_height;

    public array $roster_sort_order;

    /**
     * @var string[]
     */
    public ?array $user_hidden_fields;

    public static function group(): string
    {
        return 'dashboard';
    }
}
