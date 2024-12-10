<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Models\Enums\RosterMode;
use App\Models\Group;
use App\Services\SettingsService;
use App\Settings\DashboardSettings;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;

class Roster extends Page
{
    use HasPageShield;

    /**
     * @var string[]
     */
    public array $hiddenFields;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?int $navigationSort = 4;

    protected ?string $subheading = 'An comprehensive overview of your organization\'s personnel.';

    public function mount(): void
    {
        $this->hiddenFields = SettingsService::get(DashboardSettings::class, 'user_hidden_fields', []);
    }

    public function getView(): string
    {
        /** @var DashboardSettings $settings */
        $settings = app(DashboardSettings::class);

        if ($settings->roster_mode === RosterMode::MANUAL) {
            return 'filament.app.pages.roster.manual';
        }

        return 'filament.app.pages.roster.automatic';
    }

    protected function getViewData(): array
    {
        $settings = app(DashboardSettings::class);
        if ($settings->roster_mode === RosterMode::MANUAL) {
            $groups = Group::query()->forManualRoster()->get();
        } else {
            $groups = Group::query()->forAutomaticRoster()->get();
        }

        return [
            'groups' => $groups,
        ];
    }
}
