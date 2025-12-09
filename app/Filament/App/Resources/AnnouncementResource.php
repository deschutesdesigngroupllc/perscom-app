<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\AnnouncementResource\Pages\CreateAnnouncement;
use App\Filament\App\Resources\AnnouncementResource\Pages\EditAnnouncement;
use App\Filament\App\Resources\AnnouncementResource\Pages\ListAnnouncements;
use App\Filament\Exports\AnnouncementExporter;
use App\Forms\Components\ModelNotification;
use App\Models\Announcement;
use App\Models\Scopes\DisabledScope;
use App\Models\Scopes\EnabledScope;
use App\Services\UserSettingsService;
use App\Settings\OrganizationSettings;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use UnitEnum;

class AnnouncementResource extends BaseResource
{
    protected static ?string $model = Announcement::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';

    protected static string|UnitEnum|null $navigationGroup = 'Organization';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Announcement')
                            ->icon('heroicon-o-megaphone')
                            ->schema([
                                TextInput::make('title')
                                    ->afterStateUpdated(fn (Set $set, $state): mixed => $set('model_notifications.subject', $state))
                                    ->live(onBlur: true)
                                    ->helperText('The announcement title.')
                                    ->columnSpanFull()
                                    ->required()
                                    ->maxLength(255),
                                RichEditor::make('content')
                                    ->extraInputAttributes(['style' => 'min-height: 10rem;'])
                                    ->afterStateUpdated(fn (Set $set, $state): mixed => $set('model_notifications.message', $state))
                                    ->live(onBlur: true)
                                    ->helperText('The announcement content.')
                                    ->required()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                                ColorPicker::make('color')
                                    ->helperText('Add some color to the announcement.')
                                    ->required(),
                                Checkbox::make('global')
                                    ->helperText('If checked, the announcement will be displayed at the top of all pages.'),
                                DateTimePicker::make('expires_at')
                                    ->timezone(UserSettingsService::get('timezone', function () {
                                        /** @var OrganizationSettings $settings */
                                        $settings = app(OrganizationSettings::class);

                                        return $settings->timezone ?? config('app.timezone');
                                    }))
                                    ->label('Expiration')
                                    ->helperText('If set, the announcement will disappear after this date.'),
                            ]),
                        Tab::make('Notifications')
                            ->visible(fn ($operation): bool => $operation === 'create')
                            ->icon('heroicon-o-bell')
                            ->schema([
                                ModelNotification::make(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateIcon(Heroicon::OutlinedMegaphone)
            ->emptyStateDescription('There are no announcements to view. Create one to get started.')
            ->columns([
                TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('content')
                    ->formatStateUsing(fn ($state) => Str::limit($state))
                    ->sortable()
                    ->html()
                    ->wrap()
                    ->searchable(),
                ColorColumn::make('color')
                    ->sortable()
                    ->searchable(),
                IconColumn::make('global')
                    ->sortable(),
                ToggleColumn::make('enabled')
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label('Expires')
                    ->timezone(UserSettingsService::get('timezone', function () {
                        /** @var OrganizationSettings $settings */
                        $settings = app(OrganizationSettings::class);

                        return $settings->timezone ?? config('app.timezone');
                    }))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->recordClasses(fn (?Announcement $record): ?string => match ($record->enabled) {
                false => 'border-s-2! border-s-red-600!',
                default => null,
            })
            ->groups(['enabled', 'global'])
            ->filters([
                TernaryFilter::make('enabled'),
                TernaryFilter::make('global'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                ExportBulkAction::make()
                    ->exporter(AnnouncementExporter::class)
                    ->icon('heroicon-o-document-arrow-down'),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                EnabledScope::class,
                DisabledScope::class,
            ]);
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListAnnouncements::route('/'),
            'create' => CreateAnnouncement::route('/create'),
            'edit' => EditAnnouncement::route('/{record}/edit'),
        ];
    }

    /**
     * @param  Announcement  $record
     */
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->title;
    }

    /**
     * @param  Announcement  $record
     * @return string[]
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        if (blank($record->content)) {
            return [];
        }

        return [
            Str::of($record->content)->stripTags()->limit()->squish()->toString(),
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'content'];
    }
}
