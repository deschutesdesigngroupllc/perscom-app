<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Features\ExportDataFeature;
use App\Filament\App\Resources\FormResource\Pages;
use App\Filament\App\Resources\FormResource\RelationManagers;
use App\Filament\Exports\FormExporter;
use App\Forms\Components\ModelNotification;
use App\Models\Form as FormModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Laravel\Pennant\Feature;

class FormResource extends BaseResource
{
    protected static ?string $model = FormModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $navigationGroup = 'Forms';

    protected static ?int $navigationSort = 2;

    public static function modelNotificationCreatedEvent(): string
    {
        return 'submission.created';
    }

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
                                    ->label('Message')
                                    ->nullable()
                                    ->helperText('The message displayed when the form is successfully submitted.')
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Notifications')
                            ->icon('heroicon-o-bell')
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

    /**
     * @param  FormModel  $record
     */
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name;
    }

    /**
     * @param  FormModel  $record
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Description' => Str::of($record->description)->stripTags()->limit()->squish()->toString(),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'description'];
    }
}
