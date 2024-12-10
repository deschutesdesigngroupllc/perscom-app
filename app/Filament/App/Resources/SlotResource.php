<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\SlotResource\Pages;
use App\Models\Enums\RosterMode;
use App\Models\Scopes\HiddenScope;
use App\Models\Scopes\VisibleScope;
use App\Models\Slot;
use App\Settings\DashboardSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class SlotResource extends BaseResource
{
    protected static ?string $model = Slot::class;

    protected static ?string $navigationIcon = 'heroicon-o-cursor-arrow-rays';

    protected static ?string $navigationGroup = 'Organization';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Slot')
                            ->icon('heroicon-o-cursor-arrow-rays')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->helperText('The name of the slot.')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\RichEditor::make('description')
                                    ->helperText('A brief description of the slot.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->formatStateUsing(fn ($state) => Str::limit($state))
                    ->html()
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('hidden')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('hidden'),
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
            ->reorderable('slots.order');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
                VisibleScope::class,
                HiddenScope::class,
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSlots::route('/'),
            'create' => Pages\CreateSlot::route('/create'),
            'edit' => Pages\EditSlot::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        /** @var DashboardSettings $settings */
        $settings = app(DashboardSettings::class);

        return $settings->roster_mode === RosterMode::MANUAL && parent::canAccess();
    }
}
