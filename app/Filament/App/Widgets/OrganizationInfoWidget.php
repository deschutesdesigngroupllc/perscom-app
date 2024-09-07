<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets;

use App\Settings\DashboardSettings;
use Filament\Widgets\Widget;

class OrganizationInfoWidget extends Widget
{
    protected static ?int $sort = -4;

    protected static bool $isLazy = false;

    protected static string $view = 'filament.app.widgets.organization-info-widget';

    protected ?string $title = null;

    protected ?string $subtitle = null;

    public function mount(DashboardSettings $settings): void
    {
        $this->title = $settings->title;
        $this->subtitle = $settings->subtitle;
    }
}
