<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AlertResource\Pages\CreateAlert;
use App\Filament\Admin\Resources\AlertResource\Pages\EditAlert;
use App\Filament\Admin\Resources\AlertResource\Pages\ListAlerts;
use App\Models\Alert;
use App\Models\Enums\AlertChannel;
use App\Models\Scopes\EnabledScope;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class AlertResource extends Resource
{
    protected static ?string $model = Alert::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string|UnitEnum|null $navigationGroup = 'Communications';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Alert')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->schema([
                                TextInput::make('title')
                                    ->columnSpanFull()
                                    ->required()
                                    ->maxLength(255),
                                RichEditor::make('message')
                                    ->extraInputAttributes(['style' => 'min-height: 10rem;'])
                                    ->maxLength(65535)
                                    ->required()
                                    ->columnSpanFull(),
                                Toggle::make('enabled')
                                    ->default(true)
                                    ->required(),
                                CheckboxList::make('channels')
                                    ->hiddenLabel()
                                    ->required()
                                    ->bulkToggleable()
                                    ->descriptions(fn () => collect(AlertChannel::cases())
                                        ->mapWithKeys(fn (AlertChannel $channel) => [$channel->value => $channel->getDescription()])
                                        ->toArray())
                                    ->options(fn () => collect(AlertChannel::cases())
                                        ->mapWithKeys(fn (AlertChannel $channel) => [$channel->value => $channel->getLabel()])
                                        ->toArray()),
                            ]),
                        Tab::make('Link')
                            ->icon('heroicon-o-link')
                            ->schema([
                                TextInput::make('link_text')
                                    ->label('Text')
                                    ->maxLength(255),
                                TextInput::make('url')
                                    ->url()
                                    ->label('URL')
                                    ->requiredWith('link_text')
                                    ->maxLength(255),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateDescription('There are no alerts to display.')
            ->columns([
                TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('message')
                    ->html()
                    ->wrap()
                    ->limit()
                    ->sortable()
                    ->searchable(),
                ToggleColumn::make('enabled')
                    ->sortable(),
                TextColumn::make('channels')
                    ->badge(),
                TextColumn::make('created_at')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->groups(['enabled'])
            ->filters([
                TernaryFilter::make('enabled'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order', 'desc')
            ->reorderable('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAlerts::route('/'),
            'create' => CreateAlert::route('/create'),
            'edit' => EditAlert::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                EnabledScope::class,
            ]);
    }
}
