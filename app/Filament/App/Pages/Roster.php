<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Models\AssignmentRecord;
use App\Models\Enums\RosterMode;
use App\Models\Group;
use App\Models\Slot;
use App\Models\Unit;
use App\Services\SettingsService;
use App\Settings\DashboardSettings;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

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
            $groups = $this->mergeSecondaryAssignmentRecordsForManualRoster(
                groups: Group::query()->forManualRoster()->get()
            );
        } else {
            $groups = $this->mergeSecondaryAssignmentRecordsForAutomaticRoster(
                groups: Group::query()->forAutomaticRoster()->get()
            );
        }

        return [
            'groups' => $groups,
        ];
    }

    /**
     * @param  Collection<int, Group>  $groups
     */
    protected function mergeSecondaryAssignmentRecordsForAutomaticRoster(Collection $groups): Collection
    {
        return $groups->map(fn (Group $group) => tap($group, fn (Group $group) => data_set($group, 'units', $group->units->map(fn (Unit $unit) => tap($unit, fn (Unit $unit) => data_set($unit, 'users', $unit->users->merge($unit->secondary_assignment_records->map(function (AssignmentRecord $record) {
            $user = $record->user;

            data_set($user, 'position', $record->position);
            data_set($user, 'specialty', $record->specialty);

            return $user;
        }))))))));
    }

    /**
     * @param  Collection<int, Group>  $groups
     */
    protected function mergeSecondaryAssignmentRecordsForManualRoster(Collection $groups): Collection
    {
        return $groups->map(fn (Group $group) => tap($group, fn (Group $group) => data_set($group, 'units', $group->units->map(fn (Unit $unit) => tap($unit, fn (Unit $unit) => data_set($unit, 'slots', $unit->slots->map(fn (Slot $slot) => tap($slot, fn (Slot $slot) => data_set($slot, 'users', $slot->users->merge($slot->secondary_assignment_records->map(function (AssignmentRecord $record) use ($slot) {
            $user = $record->user;

            data_set($user, 'position', $slot->position);
            data_set($user, 'specialty', $slot->specialty);

            return $user;
        })))))))))));
    }
}
