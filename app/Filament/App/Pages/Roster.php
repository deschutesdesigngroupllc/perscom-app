<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Models\Group;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class Roster extends Page
{
    public Collection $data;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.app.pages.roster';

    public function mount(): void
    {
        $this->data = Group::query()->orderForRoster()->get();
    }
}
