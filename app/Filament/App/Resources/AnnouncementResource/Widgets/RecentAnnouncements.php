<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AnnouncementResource\Widgets;

use App\Models\Announcement;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentAnnouncements extends BaseWidget
{
    protected static ?int $sort = -2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Announcement::query()->where('global', false)->latest()->take(10)
            )
            ->recordAction('view')
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('title')
                        ->color(fn (?Announcement $record) => Color::hex($record->color))
                        ->weight(FontWeight::Bold),
                    Tables\Columns\TextColumn::make('content')
                        ->html()
                        ->wrap(),
                ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->hiddenLabel()
                    ->infolist([
                        TextEntry::make('title')
                            ->hiddenLabel()
                            ->color(fn (?Announcement $record) => Color::hex($record->color))
                            ->weight(FontWeight::Bold),
                        TextEntry::make('content')
                            ->hiddenLabel()
                            ->html(),
                    ]),
            ])
            ->emptyStateDescription('There are no recent announcements to show.')
            ->paginated(false);
    }
}
