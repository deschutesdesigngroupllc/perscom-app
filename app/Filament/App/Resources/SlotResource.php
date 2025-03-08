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
use Illuminate\Database\Eloquent\Model;
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
                        Forms\Components\Tabs\Tab::make('Assignment')
                            ->icon('heroicon-o-rectangle-stack')
                            ->schema([
                                Forms\Components\Select::make('position_id')
                                    ->helperText('If selected, a user will be assigned the position when an assignment record is created for the slot.')
                                    ->preload()
                                    ->relationship(name: 'position', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form): Form => PositionResource::form($form)),
                                Forms\Components\Select::make('specialty_id')
                                    ->helperText('If selected, a user will be assigned the specialty when an assignment record is created for the slot.')
                                    ->preload()
                                    ->relationship(name: 'specialty', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form): Form => SpecialtyResource::form($form)),
                            ]),
                        Forms\Components\Tabs\Tab::make('Roster')
                            ->icon('heroicon-o-queue-list')
                            ->schema([
                                Forms\Components\RichEditor::make('empty')
                                    ->label('Empty Message')
                                    ->helperText('Display a message when no users occupy the slot.')
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
                Tables\Columns\TextColumn::make('position.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('specialty.name')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('hidden')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('hidden'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('slots.order');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                VisibleScope::class,
                HiddenScope::class,
            ]);
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

    /**
     * @param  Slot  $record
     */
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name;
    }

    /**
     * @param  Slot  $record
     * @return string[]
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        if (blank($record->description)) {
            return [];
        }

        return [
            Str::of($record->description)->stripTags()->limit()->squish()->toString(),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'description'];
    }
}
