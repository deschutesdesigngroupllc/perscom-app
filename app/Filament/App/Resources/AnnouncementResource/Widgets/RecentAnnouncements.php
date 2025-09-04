<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AnnouncementResource\Widgets;

use App\Models\Announcement;
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
    protected static ?int $sort = -2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->recordClasses([
                'sm:-mx-6' => true,
                '-mx-4' => true,
            ])
            ->query(
                Announcement::query()->where('global', false)
                    ->latest()
            )
            ->recordAction('view')
            ->columns([
                Stack::make([
                    TextColumn::make('title')
                        ->color(fn (?Announcement $record): array => Color::generateV3Palette($record->color))
                        ->weight(FontWeight::Bold),
                    TextColumn::make('content')
                        ->html()
                        ->wrap(),
                ]),
                TextColumn::make('created_at')
                    ->toggleable(false)
                    ->color(Color::Gray),
            ])
            ->recordActions([
                ViewAction::make()
                    ->icon(null)
                    ->hiddenLabel()
                    ->schema([
                        TextEntry::make('title')
                            ->hiddenLabel()
                            ->color(fn (?Announcement $record): array => Color::generateV3Palette($record->color))
                            ->weight(FontWeight::Bold),
                        TextEntry::make('content')
                            ->hiddenLabel()
                            ->html(),
                    ]),
            ])
            ->emptyStateDescription('There are no recent announcements to show.')
            ->paginated([5]);
    }
}
