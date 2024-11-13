<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Models\Group;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class Roster extends Page
{
    use HasPageShield;

    public Collection $data;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.app.pages.roster';

    protected ?string $subheading = 'An comprehensive overview of your organization\'s personnel.';

    public function mount(): void
    {
        $this->data = Group::query()->orderForRoster()->get();
    }
}
