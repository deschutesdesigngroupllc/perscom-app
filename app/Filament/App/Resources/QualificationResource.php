<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\Categories\Schemas\CategoryForm;
use App\Filament\App\Resources\QualificationResource\Pages\CreateQualification;
use App\Filament\App\Resources\QualificationResource\Pages\EditQualification;
use App\Filament\App\Resources\QualificationResource\Pages\ListQualifications;
use App\Filament\Exports\QualificationExporter;
use App\Models\Qualification;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use UnitEnum;

class QualificationResource extends BaseResource
{
    protected static ?string $model = Qualification::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-star';

    protected static string|UnitEnum|null $navigationGroup = 'Organization';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Qualification')
                            ->icon('heroicon-o-star')
                            ->schema([
                                TextInput::make('name')
                                    ->helperText('The name of the qualification.')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('categories')
                                    ->label('Category')
                                    ->createOptionForm(fn (Schema $schema): Schema => CategoryForm::configure($schema))
                                    ->editOptionForm(fn (Schema $schema): Schema => CategoryForm::configure($schema))
                                    ->helperText('The category the qualification belongs to.')
                                    ->nullable()
                                    ->preload()
                                    ->searchable()
                                    ->multiple()
                                    ->maxItems(1)
                                    ->relationship('categories', 'name', modifyQueryUsing: fn (Builder $query): Builder => $query->where('resource', static::$model)),
                                RichEditor::make('description')
                                    ->extraInputAttributes(['style' => 'min-height: 10rem;'])
                                    ->helperText('A brief description of the qualification.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Image')
                            ->visibleOn('edit')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make()
                                    ->contained(false)
                                    ->hiddenLabel()
                                    ->relationship('image', fn ($state): bool => filled(data_get($state, 'path')))
                                    ->schema([
                                        FileUpload::make('path')
                                            ->hiddenLabel()
                                            ->image()
                                            ->imageEditor()
                                            ->previewable()
                                            ->openable()
                                            ->downloadable()
                                            ->visibility('public')
                                            ->storeFileNamesIn('filename')
                                            ->helperText('Add an optional image for the qualification.'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateIcon(Heroicon::OutlinedStar)
            ->emptyStateDescription('There are no qualifications to view. Create one to get started.')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                ImageColumn::make('image.path')
                    ->placeholder('No Image')
                    ->label('Image'),
                TextColumn::make('description')
                    ->placeholder('No Description')
                    ->formatStateUsing(fn ($state) => Str::limit($state))
                    ->html()
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('categories.name')
                    ->placeholder('No Categories')
                    ->sortable()
                    ->color('gray')
                    ->badge(),
                TextColumn::make('created_at')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->groups([
                Group::make('categoryPivot.category_id')
                    ->label('Category')
                    ->getTitleFromRecordUsing(fn (Qualification $record) => $record->categoryPivot?->category?->name),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                ExportBulkAction::make()
                    ->exporter(QualificationExporter::class)
                    ->icon('heroicon-o-document-arrow-down'),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultGroup('categoryPivot.category_id')
            ->defaultSort('order')
            ->reorderable('order');
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListQualifications::route('/'),
            'create' => CreateQualification::route('/create'),
            'edit' => EditQualification::route('/{record}/edit'),
        ];
    }

    /**
     * @param  Qualification  $record
     */
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name;
    }

    /**
     * @param  Qualification  $record
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
