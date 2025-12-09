<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\SlotResource\Pages\CreateSlot;
use App\Filament\App\Resources\SlotResource\Pages\EditSlot;
use App\Filament\App\Resources\SlotResource\Pages\ListSlots;
use App\Models\Enums\RosterMode;
use App\Models\Scopes\HiddenScope;
use App\Models\Scopes\VisibleScope;
use App\Models\Slot;
use App\Settings\DashboardSettings;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use UnitEnum;

class SlotResource extends BaseResource
{
    protected static ?string $model = Slot::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cursor-arrow-rays';

    protected static string|UnitEnum|null $navigationGroup = 'Organization';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Slot')
                            ->icon('heroicon-o-cursor-arrow-rays')
                            ->schema([
                                TextInput::make('name')
                                    ->helperText('The name of the slot.')
                                    ->required()
                                    ->maxLength(255),
                                RichEditor::make('description')
                                    ->extraInputAttributes(['style' => 'min-height: 10rem;'])
                                    ->helperText('A brief description of the slot.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Assignment')
                            ->icon('heroicon-o-rectangle-stack')
                            ->schema([
                                Select::make('position_id')
                                    ->helperText('If selected, a user will be assigned the position when an assignment record is created for the slot.')
                                    ->preload()
                                    ->relationship(name: 'position', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn (Schema $form): Schema => PositionResource::form($form)),
                                Select::make('specialty_id')
                                    ->helperText('If selected, a user will be assigned the specialty when an assignment record is created for the slot.')
                                    ->preload()
                                    ->relationship(name: 'specialty', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn (Schema $form): Schema => SpecialtyResource::form($form)),
                            ]),
                        Tab::make('Roster')
                            ->icon('heroicon-o-queue-list')
                            ->schema([
                                RichEditor::make('empty')
                                    ->extraInputAttributes(['style' => 'min-height: 10rem;'])
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
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->placeholder('No Description')
                    ->formatStateUsing(fn ($state) => Str::limit($state))
                    ->html()
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('position.name')
                    ->sortable(),
                TextColumn::make('specialty.name')
                    ->sortable(),
                ToggleColumn::make('hidden')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('hidden'),
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

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListSlots::route('/'),
            'create' => CreateSlot::route('/create'),
            'edit' => EditSlot::route('/{record}/edit'),
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

    /**
     * @return array<int, string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'description'];
    }
}
