<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AnnouncementResource\Widgets;

use App\Models\Announcement;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentAnnouncements extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = -2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->description('View you organization\'s most recent announcements.')
            ->emptyStateHeading('No recent announcements')
            ->emptyStateDescription('There are no recent announcements to show.')
            ->emptyStateIcon('heroicon-o-megaphone')
            ->query(Announcement::query()->latest())
            ->recordAction('view')
            ->columns([
                Stack::make([
                    TextColumn::make('title')
                        ->toggleable(false)
                        ->weight(FontWeight::Bold),
                    TextColumn::make('content')
                        ->toggleable(false)
                        ->html()
                        ->wrap(),
                    TextColumn::make('created_at')
                        ->toggleable(false)
                        ->color(Color::Gray),
                ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->icon(null)
                    ->hiddenLabel()
                    ->modalHeading(fn (Announcement $record): string => $record->title)
                    ->schema([
                        TextEntry::make('content')
                            ->hiddenLabel()
                            ->html(),
                    ]),
            ])
            ->paginated([5]);
    }
}
