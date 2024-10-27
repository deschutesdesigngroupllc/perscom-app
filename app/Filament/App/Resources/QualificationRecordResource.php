<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Features\ExportDataFeature;
use App\Filament\App\Resources\QualificationRecordResource\Pages;
use App\Filament\App\Resources\QualificationRecordResource\RelationManagers\AttachmentsRelationManager;
use App\Filament\App\Resources\QualificationRecordResource\RelationManagers\CommentsRelationManager;
use App\Filament\Exports\QualificationRecordExporter;
use App\Livewire\App\ViewDocument;
use App\Models\QualificationRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Pennant\Feature;

class QualificationRecordResource extends BaseResource
{
    protected static ?string $model = QualificationRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'Records';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Qualification Record Information')
                    ->columns()
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->required()
                            ->helperText('The user this record is assigned to.')
                            ->preload()
                            ->relationship(name: 'user', titleAttribute: 'name')
                            ->searchable()
                            ->createOptionForm(fn ($form) => UserResource::form($form)),
                        Forms\Components\Select::make('qualification_id')
                            ->required()
                            ->helperText('The qualification for this record.')
                            ->preload()
                            ->relationship(name: 'qualification', titleAttribute: 'name')
                            ->searchable()
                            ->createOptionForm(fn ($form) => QualificationResource::form($form)),
                        Forms\Components\RichEditor::make('text')
                            ->helperText('Optional information about the record.')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->columnSpanFull()
                            ->default(now())
                            ->required(),
                        Forms\Components\Select::make('document_id')
                            ->helperText('The document for this record.')
                            ->preload()
                            ->relationship(name: 'document', titleAttribute: 'name')
                            ->searchable()
                            ->createOptionForm(fn ($form) => DocumentResource::form($form)),
                        Forms\Components\Select::make('author_id')
                            ->required()
                            ->default(Auth::user()->getAuthIdentifier())
                            ->helperText('The author of the record.')
                            ->preload()
                            ->relationship(name: 'author', titleAttribute: 'name')
                            ->searchable()
                            ->createOptionForm(fn ($form) => UserResource::form($form)),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Infolists\Components\Tabs\Tab::make('Qualification Record')
                            ->icon('heroicon-o-star')
                            ->schema([
                                Infolists\Components\TextEntry::make('user.name'),
                                Infolists\Components\TextEntry::make('qualification.name'),
                                Infolists\Components\ImageEntry::make('qualification.image.path')
                                    ->visible(fn (?QualificationRecord $record) => isset($record->qualification->image))
                                    ->height(32)
                                    ->hiddenLabel()
                                    ->disk('s3'),
                                Infolists\Components\TextEntry::make('text')
                                    ->html()
                                    ->prose()
                                    ->columnSpanFull(),
                            ]),
                        Infolists\Components\Tabs\Tab::make('Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Infolists\Components\TextEntry::make('author.name'),
                                Infolists\Components\TextEntry::make('created_at'),
                                Infolists\Components\TextEntry::make('updated_at'),
                                Infolists\Components\TextEntry::make('deleted_at'),
                            ]),
                        Infolists\Components\Tabs\Tab::make('Document')
                            ->visible(fn (?QualificationRecord $record) => isset($record->document))
                            ->label(fn (?QualificationRecord $record) => $record->document->name ?? 'Document')
                            ->icon('heroicon-o-document')
                            ->schema([
                                Infolists\Components\Livewire::make(ViewDocument::class, fn (?QualificationRecord $record) => [
                                    'document' => $record->document,
                                    'user' => $record->user,
                                    'model' => $record,
                                ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('qualification.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ImageColumn::make('qualification.image.path')
                    ->disk('s3')
                    ->label(''),
                Tables\Columns\TextColumn::make('document.name')
                    ->icon('heroicon-o-document')
                    ->sortable()
                    ->searchable()
                    ->action(
                        Tables\Actions\Action::make('select')
                            ->visible(fn (?QualificationRecord $record) => isset($record->document))
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Close')
                            ->modalHeading(fn (?QualificationRecord $record) => $record->document->name ?? 'Document')
                            ->modalContent(fn (?QualificationRecord $record) => view('app.view-document', [
                                'document' => $record->document,
                                'user' => $record->user,
                                'model' => $record,
                            ])),
                    ),
                Tables\Columns\TextColumn::make('text')
                    ->formatStateUsing(fn ($state) => Str::limit($state))
                    ->html()
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->sortable(),
            ])
            ->groups(['user.name', 'qualification.name', 'document.name'])
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->preload()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('qualification')
                    ->relationship('qualification', 'name')
                    ->preload()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('document')
                    ->relationship('document', 'name')
                    ->preload()
                    ->multiple(),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ExportAction::make()
                        ->visible(Feature::active(ExportDataFeature::class))
                        ->exporter(QualificationRecordExporter::class),
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
            AttachmentsRelationManager::make(),
            CommentsRelationManager::make(),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQualificationRecords::route('/'),
            'create' => Pages\CreateQualificationRecord::route('/create'),
            'view' => Pages\ViewQualificationRecord::route('/{record}'),
            'edit' => Pages\EditQualificationRecord::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchResultTitle(Model|QualificationRecord $record): string
    {
        $user = optional($record->user)->name;

        return "$record->id: $user";
    }

    public static function getGlobalSearchResultDetails(Model|QualificationRecord $record): array
    {
        return [
            'Qualification' => optional($record->qualification)->name,
            'Text' => $record->text,
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['text', 'user.name', 'qualification.name'];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user', 'qualification']);
    }
}
