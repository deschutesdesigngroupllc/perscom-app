<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Features\ExportDataFeature;
use App\Filament\App\Resources\GroupResource\Pages;
use App\Filament\App\Resources\GroupResource\RelationManagers;
use App\Filament\Exports\GroupExporter;
use App\Models\Group;
use App\Models\Scopes\HiddenScope;
use App\Models\Scopes\VisibleScope;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentIconPicker\Forms\IconPicker;
use Guava\FilamentIconPicker\Tables\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Laravel\Pennant\Feature;

class GroupResource extends BaseResource
{
    protected static ?string $model = Group::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    protected static ?string $navigationGroup = 'Organization';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Group')
                            ->icon('heroicon-o-rectangle-group')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->helperText('The name of the group.')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\RichEditor::make('description')
                                    ->helperText('A brief description of the group.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Image')
                            ->visibleOn('edit')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\Section::make()
                                    ->hiddenLabel()
                                    ->relationship('image', fn ($state) => filled(data_get($state, 'path')))
                                    ->schema([
                                        Forms\Components\FileUpload::make('path')
                                            ->hiddenLabel()
                                            ->image()
                                            ->imageEditor()
                                            ->previewable()
                                            ->openable()
                                            ->downloadable()
                                            ->visibility('public')
                                            ->storeFileNamesIn('filename')
                                            ->helperText('Add an optional image for the group.'),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Roster')
                            ->icon('heroicon-o-queue-list')
                            ->schema([
                                Forms\Components\Toggle::make('hidden')
                                    ->helperText('Hide this group from the roster.')
                                    ->required(),
                                IconPicker::make('icon')
                                    ->helperText(new HtmlString('An optional icon for the group. A list of icons can be found <a href="https://heroicons.com/" target="_blank" class="underline">here</a>.')),
                                Forms\Components\RichEditor::make('empty')
                                    ->label('Empty Message')
                                    ->helperText('Display a message when no units occupy the group.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                                Forms\Components\Livewire::make(RelationManagers\UnitsRelationManager::class, fn (?Group $record): array => [
                                    'ownerRecord' => $record,
                                    'pageClass' => Pages\EditGroup::class,
                                ])->visibleOn('edit'),
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
                Tables\Columns\ImageColumn::make('image.path')
                    ->label('Image'),
                Tables\Columns\TextColumn::make('description')
                    ->formatStateUsing(fn ($state) => Str::limit($state))
                    ->html()
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('hidden')
                    ->sortable(),
                IconColumn::make('icon')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->recordClasses(fn (?Group $record): ?string => match ($record->hidden) {
                true => '!border-s-2 !border-s-red-600',
                default => null,
            })
            ->groups(['hidden'])
            ->filters([
                Tables\Filters\TernaryFilter::make('hidden'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ExportAction::make()
                        ->visible(Feature::active(ExportDataFeature::class))
                        ->exporter(GroupExporter::class),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('groups.order');
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
            'index' => Pages\ListGroups::route('/'),
            'create' => Pages\CreateGroup::route('/create'),
            'edit' => Pages\EditGroup::route('/{record}/edit'),
        ];
    }

    /**
     * @param  Group  $record
     */
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name;
    }

    /**
     * @param  Group  $record
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
