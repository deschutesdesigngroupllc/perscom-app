<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Features\ExportDataFeature;
use App\Filament\App\Resources\RankRecordResource\Pages;
use App\Filament\App\Resources\RankRecordResource\RelationManagers\AttachmentsRelationManager;
use App\Filament\App\Resources\RankRecordResource\RelationManagers\CommentsRelationManager;
use App\Filament\Exports\RankRecordExporter;
use App\Livewire\App\ViewDocument;
use App\Models\Enums\RankRecordType;
use App\Models\RankRecord;
use App\Models\User;
use App\Services\UserSettingsService;
use App\Settings\OrganizationSettings;
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

class RankRecordResource extends BaseResource
{
    protected static ?string $model = RankRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-chevron-double-up';

    protected static ?string $navigationGroup = 'Records';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Rank Record Information')
                    ->columns()
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label(fn ($operation) => $operation === 'create' ? 'User(s)' : 'User')
                            ->multiple(fn ($operation) => $operation === 'create')
                            ->required()
                            ->helperText('The user this record is assigned to.')
                            ->preload()
                            ->options(fn () => User::orderBy('name')->get()->pluck('name', 'id'))
                            ->searchable()
                            ->createOptionForm(fn ($form) => UserResource::form($form)),
                        Forms\Components\Select::make('rank_id')
                            ->required()
                            ->helperText('The rank for this record.')
                            ->preload()
                            ->relationship(name: 'rank', titleAttribute: 'name')
                            ->searchable()
                            ->createOptionForm(fn ($form) => RankResource::form($form)),
                        Forms\Components\Select::make('type')
                            ->helperText('The type of rank record.')
                            ->columnSpanFull()
                            ->options(RankRecordType::class)
                            ->required()
                            ->default(RankRecordType::PROMOTION),
                        Forms\Components\RichEditor::make('text')
                            ->helperText('Optional information about the record.')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->timezone(UserSettingsService::get('timezone', function () {
                                /** @var OrganizationSettings $settings */
                                $settings = app(OrganizationSettings::class);

                                return $settings->timezone ?? config('app.timezone');
                            }))
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
                        Infolists\Components\Tabs\Tab::make('Rank Record')
                            ->icon('heroicon-o-chevron-double-up')
                            ->schema([
                                Infolists\Components\TextEntry::make('user.name'),
                                Infolists\Components\TextEntry::make('rank.name'),
                                Infolists\Components\ImageEntry::make('rank.image.path')
                                    ->visible(fn (?RankRecord $record) => isset($record->rank->image))
                                    ->height(32)
                                    ->hiddenLabel()
                                    ->disk('s3'),
                                Infolists\Components\TextEntry::make('type')
                                    ->badge(),
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
                            ->visible(fn (?RankRecord $record) => isset($record->document))
                            ->label(fn (?RankRecord $record) => $record->document->name ?? 'Document')
                            ->icon('heroicon-o-document')
                            ->schema([
                                Infolists\Components\Livewire::make(ViewDocument::class, fn (?RankRecord $record) => [
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
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rank.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ImageColumn::make('rank.image.path')
                    ->disk('s3')
                    ->label(''),
                Tables\Columns\TextColumn::make('document.name')
                    ->icon('heroicon-o-document')
                    ->sortable()
                    ->searchable()
                    ->action(
                        Tables\Actions\Action::make('select')
                            ->visible(fn (?RankRecord $record) => isset($record->document))
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Close')
                            ->modalHeading(fn (?RankRecord $record) => $record->document->name ?? 'Document')
                            ->modalContent(fn (?RankRecord $record) => view('app.view-document', [
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
            ->groups(['user.name', 'type', 'rank.name', 'document.name'])
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->preload()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('rank')
                    ->relationship('rank', 'name')
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
                        ->exporter(RankRecordExporter::class),
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
            'index' => Pages\ListRankRecords::route('/'),
            'create' => Pages\CreateRankRecord::route('/create'),
            'view' => Pages\ViewRankRecord::route('/{record}'),
            'edit' => Pages\EditRankRecord::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchResultTitle(Model|RankRecord $record): string
    {
        $user = optional($record->user)->name;

        return "$record->id: $user";
    }

    public static function getGlobalSearchResultDetails(Model|RankRecord $record): array
    {
        return [
            'Rank' => optional($record->rank)->name,
            'Type' => optional($record->type)->getLabel(),
            'Text' => $record->text,
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['text', 'user.name', 'rank.name'];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user', 'rank']);
    }
}
