<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;

class Calendar extends Page
{
    use HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.app.pages.calendar';

    protected ?string $subheading = "Keep up-to-date with your organization's calendar of events and schedule.";
}
