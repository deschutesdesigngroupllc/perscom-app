<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Features\ExportDataFeature;
use App\Filament\App\Resources\AwardRecordResource\Pages;
use App\Filament\App\Resources\AwardRecordResource\RelationManagers\AttachmentsRelationManager;
use App\Filament\App\Resources\AwardRecordResource\RelationManagers\CommentsRelationManager;
use App\Filament\Exports\AwardRecordExporter;
use App\Forms\Components\ModelNotification;
use App\Livewire\App\ViewDocument;
use App\Models\AwardRecord;
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
use Laravel\Pennant\Feature;

class AwardRecordResource extends BaseResource
{
    protected static ?string $model = AwardRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

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
                                Forms\Components\Select::make('award_id')
                                    ->required()
                                    ->helperText('The award for this record.')
                                    ->preload()
                                    ->relationship(name: 'award', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form): Form => AwardResource::form($form)),
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
                                        alert: new HtmlString("<div class='font-bold'>The recipients will already receive a notification about the new record.</div>"),
                                        defaults: data_get($settings->toArray(), 'award_records'),
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
                        Infolists\Components\Tabs\Tab::make('Award Record')
                            ->icon('heroicon-o-trophy')
                            ->schema([
                                Infolists\Components\TextEntry::make('user.name'),
                                Infolists\Components\TextEntry::make('award.name'),
                                Infolists\Components\ImageEntry::make('award.image.path')
                                    ->visible(fn (?AwardRecord $record): bool => isset($record->award->image))
                                    ->height(32)
                                    ->hiddenLabel(),
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
                            ->visible(fn (?AwardRecord $record): bool => $record->document !== null)
                            ->label(fn (?AwardRecord $record) => $record->document->name ?? 'Document')
                            ->icon('heroicon-o-document')
                            ->schema([
                                Infolists\Components\Livewire::make(ViewDocument::class, fn (?AwardRecord $record): array => [
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
                Tables\Columns\TextColumn::make('award.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ImageColumn::make('award.image.path')
                    ->label(''),
                Tables\Columns\TextColumn::make('document.name')
                    ->icon('heroicon-o-document')
                    ->sortable()
                    ->searchable()
                    ->action(
                        Tables\Actions\Action::make('select')
                            ->visible(fn (?AwardRecord $record): bool => $record->document !== null)
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Close')
                            ->modalHeading(fn (?AwardRecord $record) => $record->document->name ?? 'Document')
                            ->modalContent(fn (?AwardRecord $record) => view('app.view-document', [
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
            ->groups(['award.name', 'document.name', 'user.name'])
            ->filters([
                Tables\Filters\SelectFilter::make('award')
                    ->relationship('award', 'name')
                    ->preload()
                    ->searchable()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('document')
                    ->relationship('document', 'name')
                    ->preload()
                    ->searchable()
                    ->multiple(),
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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ExportAction::make()
                        ->visible(Feature::active(ExportDataFeature::class))
                        ->exporter(AwardRecordExporter::class),
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
            'index' => Pages\ListAwardRecords::route('/'),
            'create' => Pages\CreateAwardRecord::route('/create'),
            'view' => Pages\ViewAwardRecord::route('/{record}'),
            'edit' => Pages\EditAwardRecord::route('/{record}/edit'),
        ];
    }

    /**
     * @param  AwardRecord  $record
     */
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        $user = optional($record->user)->name;

        return "$record->id: $user";
    }

    /**
     * @param  AwardRecord  $record
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [
            'Award' => optional($record->award)->name,
        ];

        if (filled($record->text)) {
            $details['Text'] = Str::of($record->text)->stripTags()->limit()->squish()->toString();
        }

        return $details;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['text', 'user.name', 'award.name'];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user', 'award']);
    }
}
