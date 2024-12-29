<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Features\ExportDataFeature;
use App\Filament\App\Resources\TaskResource\Pages;
use App\Filament\App\Resources\TaskResource\RelationManagers\AttachmentsRelationManager;
use App\Filament\Exports\TaskExporter;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Pennant\Feature;

class TaskResource extends BaseResource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Organization';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Task')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->helperText('The title of the task.')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\RichEditor::make('description')
                                    ->helperText('A brief description of the task.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                                Forms\Components\RichEditor::make('instructions')
                                    ->helperText('Instructions for completing the task.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Form')
                            ->icon('heroicon-o-pencil-square')
                            ->schema([
                                Forms\Components\Select::make('form_id')
                                    ->preload()
                                    ->relationship('form', 'name')
                                    ->helperText('Set to assign a form that needs to be completed as apart of the task.')
                                    ->nullable(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->formatStateUsing(fn ($state) => Str::limit($state))
                    ->html()
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ExportAction::make()
                        ->visible(Feature::active(ExportDataFeature::class))
                        ->exporter(TaskExporter::class),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AttachmentsRelationManager::make(),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }

    /**
     * @param  Task  $record
     */
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->title;
    }

    /**
     * @param  Task  $record
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Description' => Str::of($record->description)->stripTags()->limit()->squish()->toString(),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'description'];
    }
}
