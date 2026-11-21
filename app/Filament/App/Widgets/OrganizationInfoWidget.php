<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets;

use App\Filament\App\Pages\Dashboard;
use App\Models\Tenant;
use App\Models\User;
use App\Services\VersionService;
use App\Settings\OnboardingSettings;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class OrganizationInfoWidget extends Widget
{
    use HasWidgetShield {
        canView as canViewWithShield;
    }

    protected static ?int $sort = -5;

    protected static bool $isLazy = false;

    protected string $view = 'filament.app.widgets.organization-info-widget';

    protected int|string|array $columnSpan = 'full';

    protected ?string $version = null;

    protected ?string $plan = null;

    protected ?string $planColor = null;

    public static function canView(): bool
    {
        /** @var ?User $user */
        $user = Auth::user();

        return static::canViewWithShield() && (bool) $user?->hasRole(Utils::getSuperAdminName());
    }

    public function mount(): void
    {
        $this->version = VersionService::version();

        /** @var ?Tenant $tenant */
        $tenant = tenant();

        if (blank($tenant)) {
            return;
        }

        $this->plan = $tenant->subscription_plan->getLabel();
        $this->planColor = $tenant->subscription_plan->getColor();
    }

    public function canResumeOnboarding(): bool
    {
        /** @var ?User $user */
        $user = Auth::user();

        if (! $user?->hasRole(Utils::getSuperAdminName())) {
            return false;
        }

        /** @var OnboardingSettings $settings */
        $settings = resolve(OnboardingSettings::class);

        return ! $settings->isAccessible() && blank($settings->completed_at);
    }

    public function resumeOnboarding(): void
    {
        if (! $this->canResumeOnboarding()) {
            return;
        }

        resolve(OnboardingSettings::class)->resume();

        $this->redirect(Dashboard::getUrl());
    }
}
