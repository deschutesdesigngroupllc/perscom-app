<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Features\WebhookFeature;
use App\Filament\App\Resources\WebhookResource\Pages;
use App\Models\Enums\WebhookEvent;
use App\Models\Enums\WebhookMethod;
use App\Models\Webhook;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Laravel\Pennant\Feature;

class WebhookResource extends BaseResource
{
    protected static ?string $model = Webhook::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationGroup = 'Integrations';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Webhook Information')
                    ->schema([
                        Forms\Components\TextInput::make('url')
                            ->helperText('The URL that the data will be sent to.')
                            ->label('URL')
                            ->url()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('description')
                            ->helperText('A brief description of the webhook.')
                            ->nullable()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('method')
                            ->helperText('The HTTP method that will be used to send the data.')
                            ->options(WebhookMethod::class)
                            ->required()
                            ->default(WebhookMethod::POST),
                        Forms\Components\Select::make('events')
                            ->helperText('The events that will trigger the webhook.')
                            ->multiple()
                            ->options(fn () => collect(WebhookEvent::cases())->mapWithKeys(fn (WebhookEvent $event) => [$event->value => $event->value])->toArray())
                            ->required(),
                        Forms\Components\TextInput::make('secret')
                            ->revealable()
                            ->default(Str::random(32))
                            ->helperText('A secret key that will be used to sign the data.')
                            ->password()
                            ->required()
                            ->maxLength(255),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Tabs::make()
                ->columnSpanFull()
                ->tabs([
                    Tabs\Tab::make('Webhook')
                        ->icon('heroicon-o-globe-alt')
                        ->schema([
                            TextEntry::make('url')
                                ->label('URL')
                                ->copyable(),
                            TextEntry::make('description')
                                ->html(),
                            TextEntry::make('method')
                                ->badge()
                                ->color('gray'),
                            TextEntry::make('events')
                                ->listWithLineBreaks()
                                ->limitList()
                                ->expandableLimitedList()
                                ->badge(),
                            TextEntry::make('secret')
                                ->copyable(),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('url')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->formatStateUsing(fn ($state) => Str::limit($state))
                    ->sortable()
                    ->searchable()
                    ->html()
                    ->wrap(),
                Tables\Columns\TextColumn::make('method')
                    ->badge()
                    ->sortable()
                    ->color('gray')
                    ->searchable(),
                Tables\Columns\TextColumn::make('events')
                    ->listWithLineBreaks()
                    ->expandableLimitedList()
                    ->limitList()
                    ->sortable()
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups(['method'])
            ->filters([
                Tables\Filters\SelectFilter::make('events')
                    ->multiple()
                    ->searchable()
                    ->options(fn () => collect(WebhookEvent::cases())->mapWithKeys(fn (WebhookEvent $event) => [$event->value => $event->value])->toArray())
                    ->query(fn (Builder $query, $data) => $query->when(data_get($data, 'values'), fn (Builder $query) => $query->whereJsonContains('events', data_get($data, 'values')))),
                Tables\Filters\SelectFilter::make('method')
                    ->options(WebhookMethod::class),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWebhooks::route('/'),
            'create' => Pages\CreateWebhook::route('/create'),
            'edit' => Pages\EditWebhook::route('/{record}/edit'),
            'view' => Pages\ViewWebhook::route('/{record}'),
        ];
    }

    public static function canAccess(): bool
    {
        return parent::canAccess() && Feature::active(WebhookFeature::class);
    }
}
