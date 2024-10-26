<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Features\ExportDataFeature;
use App\Filament\App\Resources\AnnouncementResource\Pages;
use App\Filament\Exports\AnnouncementExporter;
use App\Models\Announcement;
use App\Models\Enums\NotificationChannel;
use App\Models\Scopes\DisabledScope;
use App\Models\Scopes\EnabledScope;
use App\Services\UserSettingsService;
use App\Settings\OrganizationSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Laravel\Pennant\Feature;

class AnnouncementResource extends BaseResource
{
    protected static ?string $model = Announcement::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'Organization';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Announcement')
                            ->icon('heroicon-o-megaphone')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->helperText('The announcement title.')
                                    ->columnSpanFull()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\RichEditor::make('content')
                                    ->helperText('The announcement content.')
                                    ->required()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                                Forms\Components\ColorPicker::make('color')
                                    ->helperText('Add some color to the announcement.')
                                    ->required(),
                                Forms\Components\Checkbox::make('global')
                                    ->helperText('If checked, the announcement will be displayed at the top of all pages.'),
                                Forms\Components\DateTimePicker::make('expires_at')
                                    ->timezone(UserSettingsService::get('timezone', function () {
                                        /** @var OrganizationSettings $settings */
                                        $settings = app(OrganizationSettings::class);

                                        return $settings->timezone ?? config('app.timezone');
                                    }))
                                    ->label('Expiration')
                                    ->helperText('If set, the announcement will disappear after this date.'),
                            ]),
                        Forms\Components\Tabs\Tab::make('Notifications')
                            ->icon('heroicon-o-bell')
                            ->schema([
                                Forms\Components\CheckboxList::make('channels')
                                    ->hiddenLabel()
                                    ->bulkToggleable()
                                    ->descriptions(function () {
                                        return collect(NotificationChannel::cases())->mapWithKeys(function (NotificationChannel $channel) {
                                            return [$channel->value => $channel->getDescription()];
                                        })->toArray();
                                    })
                                    ->options(function () {
                                        return collect(NotificationChannel::cases())->filter(function (NotificationChannel $channel) {
                                            return $channel->getEnabled();
                                        })->mapWithKeys(function (NotificationChannel $channel) {
                                            return [$channel->value => $channel->getLabel()];
                                        })->toArray();
                                    }),
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
                Tables\Columns\TextColumn::make('content')
                    ->formatStateUsing(fn ($state) => Str::limit($state))
                    ->sortable()
                    ->html()
                    ->wrap()
                    ->searchable(),
                Tables\Columns\ColorColumn::make('color')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('global')
                    ->sortable(),
                Tables\Columns\TextColumn::make('channels')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\ToggleColumn::make('enabled')
                    ->sortable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->timezone(UserSettingsService::get('timezone', function () {
                        /** @var OrganizationSettings $settings */
                        $settings = app(OrganizationSettings::class);

                        return $settings->timezone ?? config('app.timezone');
                    }))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->timezone(UserSettingsService::get('timezone', function () {
                        /** @var OrganizationSettings $settings */
                        $settings = app(OrganizationSettings::class);

                        return $settings->timezone ?? config('app.timezone');
                    }))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->timezone(UserSettingsService::get('timezone', function () {
                        /** @var OrganizationSettings $settings */
                        $settings = app(OrganizationSettings::class);

                        return $settings->timezone ?? config('app.timezone');
                    }))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->timezone(UserSettingsService::get('timezone', function () {
                        /** @var OrganizationSettings $settings */
                        $settings = app(OrganizationSettings::class);

                        return $settings->timezone ?? config('app.timezone');
                    }))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordClasses(fn (?Announcement $record) => match ($record->enabled) {
                false => '!border-s-2 !border-s-red-600',
                default => null,
            })
            ->groups(['enabled', 'global'])
            ->filters([
                Tables\Filters\TernaryFilter::make('enabled'),
                Tables\Filters\TernaryFilter::make('global'),
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
                        ->exporter(AnnouncementExporter::class),
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
                EnabledScope::class,
                DisabledScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchResultTitle(Model|Announcement $record): string
    {
        return $record->title;
    }

    public static function getGlobalSearchResultDetails(Model|Announcement $record): array
    {
        return [
            'Content' => Str::limit($record->content),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'content'];
    }
}
