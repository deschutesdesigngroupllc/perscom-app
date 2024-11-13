<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Features\ExportDataFeature;
use App\Filament\App\Resources\FormResource\Pages;
use App\Filament\App\Resources\FormResource\RelationManagers;
use App\Filament\Exports\FormExporter;
use App\Models\Form as FormModel;
use App\Models\Group;
use App\Models\Unit;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Laravel\Pennant\Feature;

class FormResource extends BaseResource
{
    protected static ?string $model = FormModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $navigationGroup = 'Forms';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Form')
                            ->icon('heroicon-o-pencil-square')
                            ->columns()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->helperText('The name of the form.')
                                    ->lazy()
                                    ->afterStateUpdated(function (Forms\Set $set, $state, $operation) {
                                        if ($operation === 'create') {
                                            $set('slug', Str::slug($state));
                                        }
                                    })
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('slug')
                                    ->helperText('The slug will be used in the URL to access the form. Allowed characters: 0-9, a-z, A-Z, or hyphen.')
                                    ->unique(ignoreRecord: true)
                                    ->regex('/^[a-zA-Z0-9-]+$/')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\RichEditor::make('description')
                                    ->helperText('A brief description of the form.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                                Forms\Components\RichEditor::make('instructions')
                                    ->helperText('Any instructions the user filling out the form should follow.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                                Forms\Components\Toggle::make('is_public')
                                    ->label('Public')
                                    ->helperText('Enable to allow guest submissions.')
                                    ->required(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Submission')
                            ->icon('heroicon-o-folder-plus')
                            ->schema([
                                Forms\Components\Select::make('submission_status_id')
                                    ->preload()
                                    ->label('Status')
                                    ->relationship('submission_status', 'name')
                                    ->helperText('The default status of the submission when it is submitted.')
                                    ->createOptionForm(fn ($form) => StatusResource::form($form)),
                                Forms\Components\Textarea::make('success_message')
                                    ->nullable()
                                    ->helperText('The message displayed when the form is successfully submitted.')
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Notifications')
                            ->icon('heroicon-o-bell')
                            ->schema([
                                Forms\Components\Section::make()
                                    ->statePath('model_notifications')
                                    ->schema([
                                        Forms\Components\Select::make('groups')
                                            ->helperText('Send a notification to a group when a form us submitted.')
                                            ->preload()
                                            ->multiple()
                                            ->searchable()
                                            ->options(fn () => Group::query()->orderBy('name')->pluck('name', 'id')),
                                        Forms\Components\Select::make('units')
                                            ->helperText('Send a notification to a unit when a form us submitted.')
                                            ->preload()
                                            ->multiple()
                                            ->searchable()
                                            ->options(fn () => Unit::query()->orderBy('name')->pluck('name', 'id')),
                                        Forms\Components\Select::make('users')
                                            ->helperText('Send a notification to a group of users when a form us submitted.')
                                            ->preload()
                                            ->multiple()
                                            ->searchable()
                                            ->options(fn () => User::query()->orderBy('name')->pluck('name', 'id')),
                                        Forms\Components\TextInput::make('subject')
                                            ->maxLength(255)
                                            ->requiredWith(['groups', 'units', 'users'])
                                            ->helperText('The subject to use with the notification.'),
                                        Forms\Components\RichEditor::make('message')
                                            ->maxLength(65535)
                                            ->requiredWith(['groups', 'units', 'users'])
                                            ->helperText('The message to use with the notification.'),
                                    ]),
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
                Tables\Columns\TextColumn::make('slug')
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->formatStateUsing(fn ($state) => Str::limit($state))
                    ->html()
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_public')
                    ->sortable()
                    ->label('Public'),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->sortable(),
            ])
            ->groups([
                Tables\Grouping\Group::make('is_public')->label('Public'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Public'),
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
                    Tables\Actions\ExportAction::make()
                        ->visible(Feature::active(ExportDataFeature::class))
                        ->exporter(FormExporter::class),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\FieldsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListForms::route('/'),
            'create' => Pages\CreateForm::route('/create'),
            'edit' => Pages\EditForm::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchResultTitle(Model|FormModel $record): string
    {
        return $record->name;
    }

    public static function getGlobalSearchResultDetails(Model|FormModel $record): array
    {
        return [
            'Description' => Str::limit($record->description),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'description'];
    }
}
