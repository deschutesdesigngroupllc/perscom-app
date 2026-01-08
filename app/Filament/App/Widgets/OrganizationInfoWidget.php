<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets;

use App\Models\Tenant;
use App\Services\VersionService;
use App\Settings\DashboardSettings;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Widget;

class OrganizationInfoWidget extends Widget
{
    use HasWidgetShield;

    protected static ?int $sort = -4;

    protected static bool $isLazy = false;

    protected string $view = 'filament.app.widgets.organization-info-widget';

    protected ?string $title = null;

    protected ?string $subtitle = null;

    protected ?string $version = null;

    protected ?string $plan = null;

    protected ?string $planColor = null;

    public function mount(DashboardSettings $settings): void
    {
        $this->title = $settings->title;
        $this->subtitle = $settings->subtitle;
        $this->version = VersionService::version();

        /** @var ?Tenant $tenant */
        $tenant = tenant();

        if (blank($tenant)) {
            return;
        }

        $this->plan = $tenant->subscription_plan->getLabel();
        $this->planColor = $tenant->subscription_plan->getColor();
    }
}
