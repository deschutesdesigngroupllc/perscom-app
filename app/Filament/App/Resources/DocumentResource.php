<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\DocumentResource\Pages;
use App\Filament\Exports\DocumentExporter;
use App\Models\Document;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DocumentResource extends BaseResource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static ?string $navigationGroup = 'Organization';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Document Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->helperText('The name of the document.')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('description')
                            ->helperText('A brief description of the document.')
                            ->hintIconTooltip('View available content tags.')
                            ->hint('Content Tags')
                            ->hintColor('gray')
                            ->hintIcon('heroicon-o-tag')
                            ->hintAction(Action::make('view')
                                ->color('gray')
                                ->modalHeading('Content Tags')
                                ->modalContent(view('app.model-tags'))
                                ->modalSubmitAction(false)
                                ->modalCancelActionLabel('Close')
                                ->modalDescription('Content tags provide a way for you to dynamically insert data into a body of text. The tags will be replaced with relevant data from whatever resource the content is attached to.')
                                ->slideOver())
                            ->nullable()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('content')
                            ->helperText('The content of the document.')
                            ->required()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('author_id')
                            ->preload()
                            ->default(Auth::user()->getAuthIdentifier())
                            ->helperText('The author of the document.')
                            ->required()
                            ->relationship('author', 'name')
                            ->columnSpanFull()
                            ->createOptionForm(fn ($form): Form => UserResource::form($form)),
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
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\ExportBulkAction::make()
                    ->exporter(DocumentExporter::class)
                    ->icon('heroicon-o-document-arrow-down'),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }

    /**
     * @param  Document  $record
     */
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name;
    }

    /**
     * @param  Document  $record
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
