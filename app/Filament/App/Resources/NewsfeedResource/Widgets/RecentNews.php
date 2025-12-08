<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\NewsfeedResource\Widgets;

use App\Models\Newsfeed;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentNews extends BaseWidget
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
            ->query(Newsfeed::query()->latest())
            ->recordAction('view')
            ->columns([
                Stack::make([
                    TextColumn::make('headline')
                        ->weight(FontWeight::Bold),
                    TextColumn::make('text')
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
                    ->modalHeading(fn (Newsfeed $record): string => $record->headline)
                    ->schema([
                        TextEntry::make('text')
                            ->hiddenLabel()
                            ->html(),
                    ]),
            ])
            ->emptyStateDescription('There are no recent announcements to show.')
            ->paginated([5]);
    }
}
