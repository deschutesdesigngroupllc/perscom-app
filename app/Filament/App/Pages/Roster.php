<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Models\Enums\RosterMode;
use App\Models\Group;
use App\Models\Position;
use App\Models\Rank;
use App\Models\Status;
use App\Models\Unit;
use App\Services\RosterService;
use App\Services\SettingsService;
use App\Settings\DashboardSettings;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;

class Roster extends Page
{
    use HasPageShield;

    /**
     * The roster layouts a viewer can switch between.
     */
    public const array DISPLAYS = ['list', 'cards', 'chart'];

    /**
     * @var string[]
     */
    public array $hiddenFields = [];

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: 'list')]
    public string $display = 'list';

    #[Url(as: 'group')]
    public ?int $activeGroupId = null;

    public ?int $rankId = null;

    public ?int $statusId = null;

    public ?int $positionId = null;

    public ?int $unitId = null;

    public bool $onlineOnly = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static ?int $navigationSort = 4;

    protected ?string $subheading = "A comprehensive overview of your organization's personnel.";

    public function mount(): void
    {
        $this->hiddenFields = SettingsService::get(DashboardSettings::class, 'user_hidden_fields', []) ?? [];

        if (! in_array($this->display, static::DISPLAYS, true)) {
            $this->display = 'list';
        }
    }

    public function getView(): string
    {
        return 'filament.app.pages.roster';
    }

    public function setDisplay(string $display): void
    {
        $this->display = in_array($display, static::DISPLAYS, true) ? $display : 'list';
    }

    public function selectGroup(int $groupId): void
    {
        $this->activeGroupId = $groupId;
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'rankId', 'statusId', 'positionId', 'unitId', 'onlineOnly']);
    }

    public function mode(): RosterMode
    {
        /** @var DashboardSettings $settings */
        $settings = resolve(DashboardSettings::class);

        return $settings->roster_mode;
    }

    /**
     * @return array{search: string, rank_id: ?int, status_id: ?int, position_id: ?int, unit_id: ?int, online: bool}
     */
    public function filters(): array
    {
        return [
            'search' => $this->search,
            'rank_id' => $this->rankId,
            'status_id' => $this->statusId,
            'position_id' => $this->positionId,
            'unit_id' => $this->unitId,
            'online' => $this->onlineOnly,
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $nodes
     */
    protected static function treeMembers(array $nodes): Collection
    {
        return collect($nodes)->flatMap(fn (array $node): Collection => collect($node['users'])
            ->merge(collect($node['slots'])->flatMap(fn (array $slot): Collection => $slot['users']))
            ->merge(static::treeMembers($node['children'])));
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $mode = $this->mode();
        $filters = $this->filters();
        $groups = RosterService::groups($mode);

        /** @var Group|null $activeGroup */
        $activeGroup = $groups->firstWhere('id', $this->activeGroupId) ?? $groups->first();

        $tree = $activeGroup instanceof Group ? RosterService::tree($activeGroup, $mode, $filters) : [];

        return [
            'mode' => $mode,
            'groups' => $groups,
            'activeGroup' => $activeGroup,
            'tree' => $tree,
            'insights' => RosterService::insights(static::treeMembers($tree)),
            'filtering' => RosterService::isFiltering($filters) || filled($this->unitId),
            'rankOptions' => Rank::query()->pluck('name', 'id'),
            'statusOptions' => Status::query()->pluck('name', 'id'),
            'positionOptions' => Position::query()->pluck('name', 'id'),
            'unitOptions' => Unit::query()->pluck('name', 'id'),
        ];
    }
}
