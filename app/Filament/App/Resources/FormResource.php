<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\Categories\Schemas\CategoryForm;
use App\Filament\App\Resources\FormResource\Actions\PublicUrlAction;
use App\Filament\App\Resources\FormResource\Pages\CreateForm;
use App\Filament\App\Resources\FormResource\Pages\EditForm;
use App\Filament\App\Resources\FormResource\Pages\ListForms;
use App\Filament\App\Resources\FormResource\RelationManagers\FieldsRelationManager;
use App\Filament\Exports\FormExporter;
use App\Forms\Components\ModelNotification;
use App\Models\Form as FormModel;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use UnitEnum;

class FormResource extends BaseResource
{
    protected static ?string $model = FormModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;

    protected static string|UnitEnum|null $navigationGroup = 'Records';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationParentItem = 'Forms';

    protected static ?string $navigationLabel = 'Form Builder';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Form')
                            ->icon(Heroicon::OutlinedPencilSquare)
                            ->columns()
                            ->schema([
                                TextInput::make('name')
                                    ->helperText('The name of the form.')
                                    ->lazy()
                                    ->afterStateUpdated(function (Set $set, $state, $operation): void {
                                        if ($operation === 'create') {
                                            $set('slug', Str::slug($state));
                                        }
                                    })
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('slug')
                                    ->helperText('The slug will be used in the URL to access the form. Allowed characters: 0-9, a-z, A-Z, or hyphen.')
                                    ->unique(ignoreRecord: true)
                                    ->regex('/^[a-zA-Z0-9-]+$/')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('categories')
                                    ->columnSpanFull()
                                    ->label('Category')
                                    ->createOptionForm(fn (Schema $schema): Schema => CategoryForm::configure($schema))
                                    ->editOptionForm(fn (Schema $schema): Schema => CategoryForm::configure($schema))
                                    ->helperText('The category the form belongs to.')
                                    ->nullable()
                                    ->preload()
                                    ->searchable()
                                    ->multiple()
                                    ->maxItems(1)
                                    ->relationship('categories', 'name', modifyQueryUsing: fn (Builder $query): Builder => $query->where('resource', static::$model)),
                                RichEditor::make('description')
                                    ->extraInputAttributes(['style' => 'min-height: 10rem;'])
                                    ->helperText('A brief description of the form.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                                RichEditor::make('instructions')
                                    ->extraInputAttributes(['style' => 'min-height: 10rem;'])
                                    ->helperText('Any instructions the member filling out the form should follow.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                                Toggle::make('is_public')
                                    ->label('Public')
                                    ->helperText('Enable to allow guest submissions.')
                                    ->required(),
                            ]),
                        Tab::make('Submission')
                            ->icon(Heroicon::OutlinedFolderPlus)
                            ->schema([
                                Select::make('submission_status_id')
                                    ->preload()
                                    ->label('Status')
                                    ->relationship('submission_status', 'name')
                                    ->helperText('The default status of the submission when it is submitted.')
                                    ->createOptionForm(fn (Schema $form): Schema => StatusResource::form($form)),
                                Textarea::make('success_message')
                                    ->label('Message')
                                    ->nullable()
                                    ->helperText('The message displayed when the form is successfully submitted.')
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Notifications')
                            ->icon(Heroicon::OutlinedBell)
                            ->schema([
                                ModelNotification::make(
                                    alert: new HtmlString("<div class='font-bold'>Enable to send notifications when a form is submitted.</div>")
                                ),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateIcon(Heroicon::OutlinedPencilSquare)
            ->emptyStateDescription('Forms collect applications, requests and reports from members. Build your first form.')
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('slug')
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->placeholder('No Description')
                    ->formatStateUsing(fn ($state): string => Str::limit($state))
                    ->html()
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                IconColumn::make('is_public')
                    ->sortable()
                    ->label('Public'),
                TextColumn::make('created_at')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->groups([
                Group::make('categoryPivot.category_id')
                    ->label('Category')
                    ->getTitleFromRecordUsing(
                        fn (FormModel $record): ?string => $record->categoryPivot?->category?->name,
                    ),
            ])
            ->filters([
                TernaryFilter::make('is_public')
                    ->label('Public'),
            ])
            ->recordActions([
                PublicUrlAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                ExportBulkAction::make()
                    ->exporter(FormExporter::class)
                    ->icon(Heroicon::OutlinedDocumentArrowDown),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultGroup('categoryPivot.category_id');
    }

    public static function getRelations(): array
    {
        return [
            FieldsRelationManager::class,
        ];
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListForms::route('/'),
            'create' => CreateForm::route('/create'),
            'edit' => EditForm::route('/{record}/edit'),
        ];
    }

    /**
     * @param  FormModel  $record
     */
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name;
    }

    /**
     * @param  FormModel  $record
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
