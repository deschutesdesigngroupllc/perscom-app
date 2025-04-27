<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\RankRecordResource\Pages;
use App\Filament\App\Resources\RankRecordResource\RelationManagers\AttachmentsRelationManager;
use App\Filament\App\Resources\RankRecordResource\RelationManagers\CommentsRelationManager;
use App\Filament\Exports\RankRecordExporter;
use App\Forms\Components\ModelNotification;
use App\Livewire\App\ViewDocument;
use App\Models\Enums\RankRecordType;
use App\Models\RankRecord;
use App\Models\User;
use App\Settings\NotificationSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

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
                Forms\Components\Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Details')
                            ->columns()
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->label(fn ($operation): string => $operation === 'create' ? 'User(s)' : 'User')
                                    ->multiple(fn ($operation): bool => $operation === 'create')
                                    ->required()
                                    ->helperText('The user this record is assigned to.')
                                    ->preload()
                                    ->options(fn () => User::orderBy('name')->get()->pluck('name', 'id'))
                                    ->searchable()
                                    ->createOptionForm(fn ($form): Form => UserResource::form($form)),
                                Forms\Components\Select::make('rank_id')
                                    ->required()
                                    ->helperText('The rank for this record.')
                                    ->preload()
                                    ->relationship(name: 'rank', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form): Form => RankResource::form($form)),
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
                                    ->columnSpanFull()
                                    ->default(now())
                                    ->required(),
                                Forms\Components\Select::make('document_id')
                                    ->helperText('The document for this record.')
                                    ->preload()
                                    ->relationship(name: 'document', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form): Form => DocumentResource::form($form)),
                                Forms\Components\Select::make('author_id')
                                    ->required()
                                    ->default(Auth::user()->getAuthIdentifier())
                                    ->helperText('The author of the record.')
                                    ->preload()
                                    ->relationship(name: 'author', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form): Form => UserResource::form($form)),
                            ]),
                        Forms\Components\Tabs\Tab::make('Notifications')
                            ->visible(fn ($operation): bool => $operation === 'create')
                            ->icon('heroicon-o-bell')
                            ->schema(function (): array {
                                /** @var NotificationSettings $settings */
                                $settings = app(NotificationSettings::class);

                                return [
                                    ModelNotification::make(
                                        alert: new HtmlString("<div class='font-bold max-w-2xl'>The recipients will already receive a notification about the new record. Default notification configuration can be set in your system settings.</div>"),
                                        defaults: data_get($settings->toArray(), 'rank_records'),
                                    ),
                                ];
                            }),
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
                                    ->visible(fn (?RankRecord $record): bool => isset($record->rank->image))
                                    ->height(32)
                                    ->hiddenLabel(),
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
                            ]),
                        Infolists\Components\Tabs\Tab::make('Document')
                            ->visible(fn (?RankRecord $record): bool => $record->document !== null)
                            ->label(fn (?RankRecord $record) => $record->document->name ?? 'Document')
                            ->icon('heroicon-o-document')
                            ->schema([
                                Infolists\Components\Livewire::make(ViewDocument::class, fn (?RankRecord $record): array => [
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
            ->emptyStateDescription('Create a new rank record to get started.')
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
                    ->label(''),
                Tables\Columns\TextColumn::make('document.name')
                    ->icon('heroicon-o-document')
                    ->sortable()
                    ->searchable()
                    ->action(
                        Tables\Actions\Action::make('select')
                            ->visible(fn (?RankRecord $record): bool => $record->document !== null)
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
            ])
            ->groups(['document.name', 'rank.name', 'type', 'user.name'])
            ->filters([
                Tables\Filters\SelectFilter::make('document')
                    ->relationship('document', 'name')
                    ->preload()
                    ->searchable()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('rank')
                    ->relationship('rank', 'name')
                    ->preload()
                    ->searchable()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('type')
                    ->options(RankRecordType::class)
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->preload()
                    ->searchable()
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\ExportBulkAction::make()
                    ->exporter(RankRecordExporter::class)
                    ->icon('heroicon-o-document-arrow-down'),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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

    /**
     * @param  RankRecord  $record
     */
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        $user = optional($record->user)->name;

        return "$record->id: $user";
    }

    /**
     * @param  RankRecord  $record
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [
            'Rank' => optional($record->rank)->name,
            'Type' => optional($record->type)->getLabel(),
        ];

        if (filled($record->text)) {
            $details['Text'] = Str::of($record->text)->stripTags()->limit()->squish()->toString();
        }

        return $details;
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
