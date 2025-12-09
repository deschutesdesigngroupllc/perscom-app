<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\FieldResource\Pages\CreateField;
use App\Filament\App\Resources\FieldResource\Pages\EditField;
use App\Filament\App\Resources\FieldResource\Pages\ListFields;
use App\Models\Enums\FieldOptionsModel;
use App\Models\Enums\FieldOptionsType;
use App\Models\Enums\FieldType;
use App\Models\Field;
use App\Models\Scopes\HiddenScope;
use App\Models\Scopes\VisibleScope;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use UnitEnum;

class FieldResource extends BaseResource
{
    protected static ?string $model = Field::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-pencil';

    protected static string|UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Field')
                            ->columns()
                            ->icon('heroicon-o-pencil')
                            ->schema([
                                TextInput::make('name')
                                    ->helperText('This is the name of the field that will be displayed to the user.')
                                    ->required()
                                    ->lazy()
                                    ->afterStateUpdated(function (Set $set, $state, $operation): void {
                                        if ($operation === 'create') {
                                            $set('key', Str::slug($state, '_'));
                                        }
                                    })
                                    ->maxLength(255),
                                TextInput::make('key')
                                    ->label('Slug')
                                    ->helperText('The slug will be used as the field key when saving a form that utilizes the field. Allowed characters: 0-9, a-z, A-Z, or underscore.')
                                    ->regex('/^\w+$/')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('type')
                                    ->helperText('The type of field.')
                                    ->options(FieldType::class)
                                    ->required()
                                    ->live()
                                    ->columnSpanFull(),
                                Radio::make('options_type')
                                    ->validationAttribute('type')
                                    ->default(FieldOptionsType::Array)
                                    ->label('Options Type')
                                    ->helperText('The source of the options.')
                                    ->options(FieldOptionsType::class)
                                    ->visible(fn (Get $get): bool => $get('type') === FieldType::FIELD_SELECT)
                                    ->requiredIf('type', 'select')
                                    ->live()
                                    ->columnSpanFull(),
                                KeyValue::make('options')
                                    ->helperText('The options for the input.')
                                    ->columnSpanFull()
                                    ->formatStateUsing(fn (?Field $record, $state): mixed => filled($record?->getRawOriginal('options')) ? json_decode((string) $record->getRawOriginal('options'), true) : null)
                                    ->visible(fn (Get $get): bool => $get('type') === FieldType::FIELD_SELECT && $get('options_type') === FieldOptionsType::Array)
                                    ->required(fn (Get $get): bool => $get('options_type') === FieldOptionsType::Array)
                                    ->dehydrateStateUsing(fn ($state): string => Collection::wrap($state)->filter()->toJson()),
                                Select::make('options_model')
                                    ->validationAttribute('resource')
                                    ->label('Resource')
                                    ->helperText('The resource to use for the options.')
                                    ->options(FieldOptionsModel::class)
                                    ->columnSpanFull()
                                    ->required(fn (Get $get): bool => $get('options_type') === FieldOptionsType::Model)
                                    ->visible(fn (Get $get): bool => $get('type') === FieldType::FIELD_SELECT && $get('options_type') === FieldOptionsType::Model),
                                RichEditor::make('description')
                                    ->extraInputAttributes(['style' => 'min-height: 10rem;'])
                                    ->helperText('A optional brief description of the field.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),

                            ]),
                        Tab::make('Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextInput::make('placeholder')
                                    ->helperText('If a text type field, this text will fill the field when no value is present.')
                                    ->nullable()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                TextInput::make('help')
                                    ->helperText('ModelLike this text, this is a short description that should help the user fill out the field.')
                                    ->nullable()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Validation')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                TextInput::make('rules')
                                    ->helperText(new HtmlString('A pipe delimited list of validation rules that can be found <a href="https://laravel.com/docs/11.x/validation#available-validation-rules" target="_blank" class="underline">here</a>.'))
                                    ->nullable()
                                    ->maxLength(255),
                                Toggle::make('required')
                                    ->helperText('The field will be required to be filled out.')
                                    ->required(),
                                Toggle::make('readonly')
                                    ->helperText('A readonly input field cannot be modified (however, a user can tab to it, highlight it, and copy the text from it). Useful in combination with a placeholder.')
                                    ->required(),
                            ]),
                        Tab::make('Visibility')
                            ->icon('heroicon-o-eye')
                            ->schema([
                                Toggle::make('hidden')
                                    ->helperText('The field will only be shown if the user has editable permissions.')
                                    ->required(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateDescription('There are no custom fields to view. Create one to get started.')
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('key')
                    ->badge()
                    ->copyable()
                    ->color('gray')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('type')
                    ->sortable()
                    ->badge()
                    ->searchable(),
                TextColumn::make('description')
                    ->placeholder('No Description')
                    ->formatStateUsing(fn ($state) => Str::limit($state))
                    ->sortable()
                    ->html()
                    ->wrap()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->groups(['hidden', 'readonly', 'required', 'type'])
            ->filters([
                TernaryFilter::make('hidden'),
                TernaryFilter::make('readonly'),
                TernaryFilter::make('required'),
                SelectFilter::make('type')
                    ->multiple()
                    ->options(FieldType::class),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function relationTable(Table $table): Table
    {
        return $table
            ->description('The custom fields that belong to the resource.')
            ->emptyStateDescription('Attach or create a new field to get started.')
            ->modifyQueryUsing(function (Builder $query): void {
                $query->withoutGlobalScopes([
                    VisibleScope::class,
                    HiddenScope::class,
                ]);
            })
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Add field')
                    ->modalHeading('Add Field')
                    ->modalWidth(Width::TwoExtraLarge)
                    ->modalSubmitActionLabel('Add')
                    ->attachAnother(false)
                    ->multiple()
                    ->modalDescription('Attach a custom field to this resource.')
                    ->preloadRecordSelect(),
            ])
            ->recordActions([
                DetachAction::make()
                    ->modalHeading('Remove Field')
                    ->label('Remove field'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->emptyStateActions([
                Action::make('create')
                    ->label('New field')
                    ->openUrlInNewTab()
                    ->url(FieldResource::getUrl('create'))
                    ->button(),
            ]);
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
            'index' => ListFields::route('/'),
            'create' => CreateField::route('/create'),
            'edit' => EditField::route('/{record}/edit'),
        ];
    }
}
