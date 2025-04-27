<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\FieldResource\Pages;
use App\Models\Enums\FieldOptionsModel;
use App\Models\Enums\FieldOptionsType;
use App\Models\Enums\FieldType;
use App\Models\Field;
use App\Models\Scopes\HiddenScope;
use App\Models\Scopes\VisibleScope;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class FieldResource extends BaseResource
{
    protected static ?string $model = Field::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Field')
                            ->columns()
                            ->icon('heroicon-o-pencil')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->helperText('This is the name of the field that will be displayed to the user.')
                                    ->required()
                                    ->lazy()
                                    ->afterStateUpdated(function (Forms\Set $set, $state, $operation): void {
                                        if ($operation === 'create') {
                                            $set('key', Str::slug($state, '_'));
                                        }
                                    })
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('key')
                                    ->label('Slug')
                                    ->helperText('The slug will be used as the field key when saving a form that utilizes the field. Allowed characters: 0-9, a-z, A-Z, or underscore.')
                                    ->regex('/^\w+$/')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Select::make('type')
                                    ->helperText('The type of field.')
                                    ->options(FieldType::class)
                                    ->required()
                                    ->live()
                                    ->columnSpanFull(),
                                Forms\Components\Radio::make('options_type')
                                    ->validationAttribute('type')
                                    ->default(FieldOptionsType::Array->value)
                                    ->label('Options Type')
                                    ->helperText('The source of the options.')
                                    ->options(FieldOptionsType::class)
                                    ->visible(fn (Forms\Get $get): bool => $get('type') === FieldType::FIELD_SELECT->value)
                                    ->requiredIf('type', 'select')
                                    ->live()
                                    ->columnSpanFull(),
                                Forms\Components\KeyValue::make('options')
                                    ->helperText('The options for the input.')
                                    ->columnSpanFull()
                                    ->visible(fn (Forms\Get $get): bool => $get('type') === FieldType::FIELD_SELECT->value && $get('options_type') === FieldOptionsType::Array->value)
                                    ->requiredIf('options_type', FieldOptionsType::Array->value)
                                    ->dehydrateStateUsing(fn ($state): array => Collection::wrap($state)->filter()->toArray()),
                                Forms\Components\Select::make('options_model')
                                    ->validationAttribute('resource')
                                    ->label('Resource')
                                    ->helperText('The resource to use for the options.')
                                    ->options(FieldOptionsModel::class)
                                    ->columnSpanFull()
                                    ->visible(fn (Forms\Get $get): bool => $get('type') === FieldType::FIELD_SELECT->value && $get('options_type') === FieldOptionsType::Model->value)
                                    ->requiredIf('options_type', FieldOptionsType::Model->value),
                                Forms\Components\RichEditor::make('description')
                                    ->helperText('A optional brief description of the field.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),

                            ]),
                        Forms\Components\Tabs\Tab::make('Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\TextInput::make('placeholder')
                                    ->helperText('If a text type field, this text will fill the field when no value is present.')
                                    ->nullable()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('help')
                                    ->helperText('ModelLike this text, this is a short description that should help the user fill out the field.')
                                    ->nullable()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Validation')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Forms\Components\TextInput::make('rules')
                                    ->helperText(new HtmlString('A pipe delimited list of validation rules that can be found <a href="https://laravel.com/docs/11.x/validation#available-validation-rules" target="_blank" class="underline">here</a>.'))
                                    ->nullable()
                                    ->maxLength(255),
                                Forms\Components\Toggle::make('required')
                                    ->helperText('The field will be required to be filled out.')
                                    ->required(),
                                Forms\Components\Toggle::make('readonly')
                                    ->helperText('A readonly input field cannot be modified (however, a user can tab to it, highlight it, and copy the text from it). Useful in combination with a placeholder.')
                                    ->required(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Visibility')
                            ->icon('heroicon-o-eye')
                            ->schema([
                                Forms\Components\Toggle::make('hidden')
                                    ->helperText('The field will only be shown if the user has editable permissions.')
                                    ->required(),
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
                Tables\Columns\TextColumn::make('key')
                    ->badge()
                    ->copyable()
                    ->color('gray')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->sortable()
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->formatStateUsing(fn ($state) => Str::limit($state))
                    ->sortable()
                    ->html()
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->groups(['hidden', 'readonly', 'required', 'type'])
            ->filters([
                Tables\Filters\TernaryFilter::make('hidden'),
                Tables\Filters\TernaryFilter::make('readonly'),
                Tables\Filters\TernaryFilter::make('required'),
                Tables\Filters\SelectFilter::make('type')
                    ->multiple()
                    ->options(FieldType::class),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
                Tables\Columns\TextColumn::make('name'),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Add field')
                    ->modalHeading('Add Field')
                    ->modalWidth(MaxWidth::TwoExtraLarge)
                    ->modalSubmitActionLabel('Add')
                    ->attachAnother(false)
                    ->multiple()
                    ->modalDescription('Attach a custom field to this resource.')
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->modalHeading('Remove Field')
                    ->label('Remove field'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFields::route('/'),
            'create' => Pages\CreateField::route('/create'),
            'edit' => Pages\EditField::route('/{record}/edit'),
        ];
    }
}
