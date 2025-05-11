<?php

declare(strict_types=1);

namespace App\Livewire\Widgets\Roster;

use App\Models\Enums\RosterMode;
use App\Models\Group;
use App\Services\RosterService;
use App\Services\SettingsService;
use App\Settings\DashboardSettings;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Index extends Component
{
    /**
     * @var string[]
     */
    public array $hiddenFields;

    public function mount(): void
    {
        $this->hiddenFields = SettingsService::get(DashboardSettings::class, 'user_hidden_fields', []);
    }

    public function render(): View
    {
        $settings = app(DashboardSettings::class);
        if ($settings->roster_mode === RosterMode::MANUAL) {
            $groups = RosterService::mergeSecondaryAssignmentRecordsForManualRoster(
                groups: Group::query()->forManualRoster()->get()
            );
        } else {
            $groups = RosterService::mergeSecondaryAssignmentRecordsForAutomaticRoster(
                groups: Group::query()->forAutomaticRoster()->get()
            );
        }

        return view('livewire.widgets.roster.index', [
            'groups' => $groups,
        ]);
    }
}
