<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AlertResource\Pages;
use App\Models\Alert;
use App\Models\Enums\AlertChannel;
use App\Models\Scopes\EnabledScope;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AlertResource extends Resource
{
    protected static ?string $model = Alert::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Communications';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Alert')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->columnSpanFull()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\RichEditor::make('message')
                                    ->maxLength(65535)
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\Toggle::make('enabled')
                                    ->default(true)
                                    ->required(),
                                Forms\Components\CheckboxList::make('channels')
                                    ->hiddenLabel()
                                    ->required()
                                    ->bulkToggleable()
                                    ->descriptions(function () {
                                        return collect(AlertChannel::cases())
                                            ->mapWithKeys(fn (AlertChannel $channel) => [$channel->value => $channel->getDescription()])
                                            ->toArray();
                                    })
                                    ->options(function () {
                                        return collect(AlertChannel::cases())
                                            ->mapWithKeys(fn (AlertChannel $channel) => [$channel->value => $channel->getLabel()])
                                            ->toArray();
                                    }),
                            ]),
                        Forms\Components\Tabs\Tab::make('Link')
                            ->icon('heroicon-o-link')
                            ->schema([
                                Forms\Components\TextInput::make('link_text')
                                    ->label('Text')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('url')
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
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('message')
                    ->html()
                    ->wrap()
                    ->limit()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('enabled')
                    ->sortable(),
                Tables\Columns\TextColumn::make('channels')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups(['enabled'])
            ->filters([
                Tables\Filters\TernaryFilter::make('enabled'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('order', 'desc')
            ->reorderable('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAlerts::route('/'),
            'create' => Pages\CreateAlert::route('/create'),
            'edit' => Pages\EditAlert::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                EnabledScope::class,
                SoftDeletingScope::class,
            ]);
    }
}
