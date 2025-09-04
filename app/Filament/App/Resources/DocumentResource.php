<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\DocumentResource\Pages\CreateDocument;
use App\Filament\App\Resources\DocumentResource\Pages\EditDocument;
use App\Filament\App\Resources\DocumentResource\Pages\ListDocuments;
use App\Filament\Exports\DocumentExporter;
use App\Models\Document;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use UnitEnum;

class DocumentResource extends BaseResource
{
    protected static ?string $model = Document::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document';

    protected static string|UnitEnum|null $navigationGroup = 'Organization';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Document Information')
                    ->schema([
                        TextInput::make('name')
                            ->helperText('The name of the document.')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        RichEditor::make('description')
                            ->helperText('A brief description of the document.')
                            ->nullable()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        RichEditor::make('content')
                            ->helperText('The content of the document. Use content tags to dynamically inject data from attached resources.')
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
                            ->required()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Select::make('author_id')
                            ->preload()
                            ->default(Auth::user()->getAuthIdentifier())
                            ->helperText('The author of the document.')
                            ->required()
                            ->relationship('author', 'name')
                            ->columnSpanFull()
                            ->createOptionForm(fn ($form): Schema => UserResource::form($form)),
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
                    ->formatStateUsing(fn ($state) => Str::limit($state))
                    ->html()
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->filters([
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                ExportBulkAction::make()
                    ->exporter(DocumentExporter::class)
                    ->icon('heroicon-o-document-arrow-down'),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDocuments::route('/'),
            'create' => CreateDocument::route('/create'),
            'edit' => EditDocument::route('/{record}/edit'),
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
