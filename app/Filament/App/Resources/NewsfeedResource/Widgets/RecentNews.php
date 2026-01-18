<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\NewsfeedResource\Widgets;

use App\Models\Newsfeed;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
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
    use HasWidgetShield;

    protected static ?int $sort = -2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->description("View you organization's most recent news.")
            ->emptyStateHeading('No recent news')
            ->emptyStateDescription('There are no recent news items to show.')
            ->emptyStateIcon('heroicon-o-newspaper')
            ->query(Newsfeed::query()->latest())
            ->recordAction('view')
            ->paginated([5])
            ->columns([
                Stack::make([
                    TextColumn::make('headline')
                        ->toggleable(false)
                        ->weight(FontWeight::Bold),
                    TextColumn::make('text')
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
                    ->modalHeading(fn (Newsfeed $record): string => $record->headline)
                    ->schema([
                        TextEntry::make('text')
                            ->hiddenLabel()
                            ->html(),
                    ]),
            ]);
    }
}
