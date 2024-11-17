<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Models\Group;
use App\Services\SettingsService;
use App\Settings\DashboardSettings;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class Roster extends Page
{
    use HasPageShield;

    /**
     * @var Collection<int, Group>
     */
    public Collection $data;

    /**
     * @var string[]
     */
    public array $hiddenFields;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.app.pages.roster';

    protected ?string $subheading = 'An comprehensive overview of your organization\'s personnel.';

    public function mount(): void
    {
        $this->data = Group::query()->orderForRoster()->get();
        $this->hiddenFields = SettingsService::get(DashboardSettings::class, 'user_hidden_fields');
    }
}
