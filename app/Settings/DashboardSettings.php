<?php

declare(strict_types=1);

namespace App\Settings;

use App\Models\Enums\RosterMode;
use Spatie\LaravelSettings\Settings;

class DashboardSettings extends Settings
{
    public string $title;

    public ?string $subtitle = null;

    public ?string $subdomain = null;

    public ?string $domain = null;

    public int $cover_photo_height;

    public array $roster_sort_order;

    public RosterMode $roster_mode;

    /**
     * @var string[]
     */
    public ?array $user_hidden_fields = null;

    public static function group(): string
    {
        return 'dashboard';
    }
}
