<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\UserResource\Filters\AwardsFilter;
use App\Filament\App\Resources\UserResource\Filters\CredentialsFilter;
use App\Filament\App\Resources\UserResource\Filters\QualificationsFilter;
use App\Filament\App\Resources\UserResource\Pages\CreateUser;
use App\Filament\App\Resources\UserResource\Pages\EditUser;
use App\Filament\App\Resources\UserResource\Pages\ListUsers;
use App\Filament\App\Resources\UserResource\Pages\ViewUser;
use App\Filament\Exports\UserExporter;
use App\Models\User;
use App\Services\SettingsService;
use App\Services\UserSettingsService;
use App\Settings\DashboardSettings;
use App\Settings\OrganizationSettings;
use App\Traits\Filament\BuildsCustomFieldComponents;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use UnitEnum;

class UserResource extends BaseResource
{
    use BuildsCustomFieldComponents;

    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|UnitEnum|null $navigationGroup = 'Personnel';

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'display_name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Demographics')
                            ->icon('heroicon-o-user')
                            ->columns()
                            ->schema([
                                Grid::make(8)
                                    ->columnSpanFull()
                                    ->schema([
                                        FileUpload::make('profile_photo')
                                            ->label('Profile Photo')
                                            ->columnSpan(1)
                                            ->visibility('public')
                                            ->image()
                                            ->imageEditor()
                                            ->downloadable()
                                            ->openable()
                                            ->avatar()
                                            ->previewable(),
                                        Grid::make(1)
                                            ->columnSpan(7)
                                            ->schema([
                                                TextInput::make('name')
                                                    ->required()
                                                    ->columnSpanFull()
                                                    ->maxLength(255),
                                                TextInput::make('email')
                                                    ->unique(ignoreRecord: true)
                                                    ->email()
                                                    ->required()
                                                    ->columnSpanFull()
                                                    ->maxLength(255),
                                            ]),
                                    ]),
                                FileUpload::make('cover_photo')
                                    ->label('Cover Photo')
                                    ->columnSpanFull()
                                    ->visibility('public')
                                    ->image()
                                    ->imageEditor()
                                    ->downloadable()
                                    ->openable()
                                    ->previewable(),
                            ]),
                        Tab::make('Assignment')
                            ->badge(fn (?User $record) => $record?->position?->name)
                            ->icon('heroicon-o-rectangle-stack')
                            ->columns()
                            ->schema([
                                Select::make('position_id')
                                    ->helperText("The user's current position.")
                                    ->columnSpanFull()
                                    ->preload()
                                    ->relationship(name: 'position', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn (Schema $form): Schema => PositionResource::form($form)),
                                Select::make('specialty_id')
                                    ->helperText("The user's current specialty.")
                                    ->columnSpanFull()
                                    ->preload()
                                    ->relationship(name: 'specialty', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn (Schema $form): Schema => SpecialtyResource::form($form)),
                                Select::make('unit_id')
                                    ->helperText("The user's current unit.")
                                    ->columnSpanFull()
                                    ->preload()
                                    ->relationship(name: 'unit', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn (Schema $form): Schema => UnitResource::form($form)),
                            ]),
                        Tab::make('Fields')
                            ->hiddenOn('create')
                            ->icon('heroicon-o-pencil')
                            ->schema(fn (?User $record): array => UserResource::buildCustomFieldInputs($record?->fields ?? collect())),
                        Tab::make('Details')
                            ->hiddenOn('create')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                DateTimePicker::make('created_at')
                                    ->default(now())
                                    ->required()
                                    ->helperText('The date the user profile was created. Used to calculate time in service.'),
                                DateTimePicker::make('updated_at')
                                    ->default(now())
                                    ->required()
                                    ->helperText('The date the user profile was last updated.'),
                                DateTimePicker::make('last_seen_at')
                                    ->timezone(UserSettingsService::get('timezone', function () {
                                        /** @var OrganizationSettings $settings */
                                        $settings = resolve(OrganizationSettings::class);

                                        return $settings->timezone ?? config('app.timezone');
                                    }))
                                    ->label('Last Online')
                                    ->helperText('The date the user last logged in.'),
                                DateTimePicker::make('email_verified_at')
                                    ->timezone(UserSettingsService::get('timezone', function () {
                                        /** @var OrganizationSettings $settings */
                                        $settings = resolve(OrganizationSettings::class);

                                        return $settings->timezone ?? config('app.timezone');
                                    }))
                                    ->label('Email Verified')
                                    ->helperText("The date the user's email was verified. Set this to bypass user email verification."),
                            ]),
                        Tab::make('Notes')
                            ->icon('heroicon-o-pencil-square')
                            ->schema([
                                RichEditor::make('notes')
                                    ->extraInputAttributes(['style' => 'min-height: 10rem;'])
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull()
                                    ->helperText('Use this optional area to keep notes on the user.'),
                            ]),
                        Tab::make('Rank')
                            ->badge(fn (?User $record) => $record?->rank?->name)
                            ->icon('heroicon-o-chevron-double-up')
                            ->columns()
                            ->schema([
                                Select::make('rank_id')
                                    ->helperText("The user's current rank.")
                                    ->preload()
                                    ->columnSpanFull()
                                    ->relationship(name: 'rank', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn (Schema $form): Schema => RankResource::form($form)),
                            ]),
                        Tab::make('Status')
                            ->badge(fn (?User $record) => $record?->status?->name)
                            ->badgeColor(fn (?User $record): array => Color::generateV3Palette($record->status->color ?? '#2563eb'))
                            ->icon('heroicon-o-scale')
                            ->schema([
                                Select::make('status_id')
                                    ->helperText("The user's current status.")
                                    ->preload()
                                    ->relationship(name: 'status', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn (Schema $form): Schema => StatusResource::form($form)),
                                Toggle::make('approved')
                                    ->visible(fn (?User $record): bool => Auth::user()->can('approve', $record))
                                    ->helperText('Turn off to disable account access.')
                                    ->required()
                                    ->default(true),
                            ]),
                    ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        $hiddenFields = Arr::wrap(SettingsService::get(DashboardSettings::class, 'user_hidden_fields', []));

        return $schema
            ->components([
                View::make('filament.app.infolists.user-hero')
                    ->viewData(fn (User $record): array => [
                        'user' => $record,
                        'coverHeight' => resolve(DashboardSettings::class)->cover_photo_height ?? 224,
                        'hiddenFields' => $hiddenFields,
                    ])
                    ->columnSpanFull(),
                Tabs::make()
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Profile')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Fieldset::make('Identity')
                                    ->columns(['default' => 1, 'md' => 2])
                                    ->schema([
                                        TextEntry::make('display_name')
                                            ->label('Name')
                                            ->hidden(fn (): bool => in_array('name', $hiddenFields)),
                                        TextEntry::make('email')
                                            ->label('Email')
                                            ->icon('heroicon-o-envelope')
                                            ->copyable()
                                            ->placeholder('—')
                                            ->hidden(fn (): bool => in_array('email', $hiddenFields)),
                                        TextEntry::make('email_verified_at')
                                            ->label('Email Verified')
                                            ->placeholder('Not verified')
                                            ->dateTime()
                                            ->hidden(fn (): bool => in_array('email_verified_at', $hiddenFields)),
                                        TextEntry::make('created_at')
                                            ->label('Member Since')
                                            ->placeholder('—')
                                            ->dateTime()
                                            ->hidden(fn (): bool => in_array('created_at', $hiddenFields)),
                                    ]),
                                Fieldset::make('Service')
                                    ->columns(['default' => 1, 'md' => 2])
                                    ->schema([
                                        TextEntry::make('last_rank_change_date')
                                            ->label('Last Rank Change')
                                            ->dateTime()
                                            ->placeholder('—')
                                            ->hidden(fn (): bool => in_array('last_rank_change_date', $hiddenFields)),
                                        TextEntry::make('last_assignment_change_date')
                                            ->label('Last Assignment Change')
                                            ->dateTime()
                                            ->placeholder('—')
                                            ->hidden(fn (): bool => in_array('last_assignment_change_date', $hiddenFields)),
                                    ]),
                            ]),
                        Tab::make('Assignment')
                            ->icon('heroicon-o-rectangle-stack')
                            ->visible(fn (): bool => ! in_array('assignment', $hiddenFields))
                            ->schema([
                                Fieldset::make('Primary Assignment')
                                    ->columns(['default' => 1, 'md' => 3])
                                    ->schema([
                                        TextEntry::make('position.name')
                                            ->label('Position')
                                            ->placeholder('No Position')
                                            ->badge()
                                            ->color('gray')
                                            ->hidden(fn (): bool => in_array('position_id', $hiddenFields)),
                                        TextEntry::make('specialty.name')
                                            ->label('Specialty')
                                            ->placeholder('No Specialty')
                                            ->badge()
                                            ->color('gray')
                                            ->hidden(fn (): bool => in_array('specialty_id', $hiddenFields)),
                                        TextEntry::make('unit.name')
                                            ->label('Unit')
                                            ->placeholder('No Unit')
                                            ->badge()
                                            ->color('gray')
                                            ->hidden(fn (): bool => in_array('unit_id', $hiddenFields)),
                                        TextEntry::make('rank.name')
                                            ->label('Rank')
                                            ->placeholder('No Rank')
                                            ->badge()
                                            ->color('gray')
                                            ->hidden(fn (): bool => in_array('rank_id', $hiddenFields))
                                            ->prefix(fn (?User $record) => optional($record?->rank?->image?->image_url, fn (string $url): HtmlString => new HtmlString(sprintf("<img src='%s' class='h-5 inline mr-1' alt='%s' />", $url, $record->rank->name)))),
                                        TextEntry::make('status.name')
                                            ->label('Status')
                                            ->placeholder('No Status')
                                            ->badge()
                                            ->color(fn (?User $record): array => Color::generateV3Palette($record?->status?->color ?? '#6b7280'))
                                            ->hidden(fn (): bool => in_array('status_id', $hiddenFields)),
                                        TextEntry::make('approved')
                                            ->label('Approved')
                                            ->badge()
                                            ->state(fn (?User $record): string => $record?->approved ? 'Yes' : 'No')
                                            ->color(fn (?User $record): string => $record?->approved ? 'success' : 'warning')
                                            ->hidden(fn (): bool => in_array('approved', $hiddenFields)),
                                    ]),
                                Section::make('Secondary Assignments')
                                    ->contained(false)
                                    ->hidden(fn (): bool => in_array('secondary_assignment_records', $hiddenFields))
                                    ->schema([
                                        RepeatableEntry::make('secondary_assignment_records')
                                            ->placeholder('No Secondary Assignment Records')
                                            ->hiddenLabel()
                                            ->table([
                                                TableColumn::make('Position'),
                                                TableColumn::make('Specialty'),
                                                TableColumn::make('Unit'),
                                                TableColumn::make('Status'),
                                            ])
                                            ->schema([
                                                TextEntry::make('position.name')
                                                    ->weight(FontWeight::Bold)
                                                    ->placeholder(new HtmlString('&ndash;')),
                                                TextEntry::make('specialty.name')
                                                    ->placeholder(new HtmlString('&ndash;')),
                                                TextEntry::make('unit.name')
                                                    ->placeholder(new HtmlString('&ndash;')),
                                                TextEntry::make('status.name')
                                                    ->badge()
                                                    ->placeholder(new HtmlString('&ndash;')),
                                            ]),
                                    ]),
                            ]),
                        Tab::make('Awards')
                            ->icon('heroicon-o-trophy')
                            ->badge(fn (?User $record): ?int => $record?->awards?->count() ?: null)
                            ->badgeColor('warning')
                            ->visible(fn (): bool => ! in_array('award_records', $hiddenFields))
                            ->schema([
                                View::make('filament.app.infolists.user-image-grid')
                                    ->columnSpanFull()
                                    ->viewData(fn (User $record): array => [
                                        'items' => $record->awards,
                                        'emptyMessage' => 'No awards have been received yet.',
                                        'fallbackIcon' => 'heroicon-o-trophy',
                                        'storageKey' => 'user-awards-view',
                                    ]),
                            ]),
                        Tab::make('Qualifications')
                            ->icon('heroicon-o-star')
                            ->badge(fn (?User $record): ?int => $record?->qualifications?->count() ?: null)
                            ->badgeColor('success')
                            ->visible(fn (): bool => ! in_array('qualification_records', $hiddenFields))
                            ->schema([
                                View::make('filament.app.infolists.user-image-grid')
                                    ->columnSpanFull()
                                    ->viewData(fn (User $record): array => [
                                        'items' => $record->qualifications,
                                        'emptyMessage' => 'No qualifications have been earned yet.',
                                        'fallbackIcon' => 'heroicon-o-star',
                                    ]),
                            ]),
                        Tab::make('Credentials')
                            ->icon('heroicon-o-identification')
                            ->badge(fn (?User $record): ?int => $record?->credentials?->count() ?: null)
                            ->badgeColor('info')
                            ->visible(fn (): bool => ! in_array('credentials', $hiddenFields))
                            ->schema([
                                View::make('filament.app.infolists.user-credential-list')
                                    ->columnSpanFull()
                                    ->viewData(fn (User $record): array => [
                                        'items' => $record->credentials,
                                        'emptyMessage' => 'No credentials have been assigned yet.',
                                    ]),
                            ]),
                        Tab::make('Custom Fields')
                            ->icon('heroicon-o-pencil-square')
                            ->visible(fn (User $record): bool => $record->fields->isNotEmpty())
                            ->schema(fn (User $record): array => UserResource::buildCustomFieldEntries($record->fields)),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $hiddenFields = Arr::wrap(SettingsService::get(DashboardSettings::class, 'user_hidden_fields', []));

        return $table
            ->emptyStateIcon(Heroicon::OutlinedUser)
            ->emptyStateDescription('There are no users to view. Create one to get started.')
            ->columns([
                ImageColumn::make('profile_photo')
                    ->placeholder('No Profile Photo')
                    ->hidden(fn (): bool => in_array('profile_photo', $hiddenFields))
                    ->label('')
                    ->defaultImageUrl(fn (User $record) => $record->profile_photo_url),
                TextColumn::make('display_name')
                    ->label('Name')
                    ->hidden(fn (): bool => in_array('name', $hiddenFields))
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('name', $direction))
                    ->searchable(query: fn (Builder $query, string $search) => $query->where('name', 'like', sprintf('%%%s%%', $search)))
                    ->icon(fn (?User $record): ?string => ! $record->approved && Auth::user()->can('approve', $record) ? 'heroicon-o-exclamation-circle' : null)
                    ->iconColor('danger')
                    ->iconPosition(IconPosition::After),
                TextColumn::make('email')
                    ->copyable()
                    ->hidden(fn (): bool => in_array('email', $hiddenFields))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('online')
                    ->hidden(fn (): bool => in_array('online', $hiddenFields))
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        true => 'success',
                        default => 'info',
                    })
                    ->formatStateUsing(fn ($state): string => match ($state) {
                        true => 'Online',
                        default => 'Offline',
                    }),
                TextColumn::make('position.name')
                    ->placeholder('No Position')
                    ->hidden(fn (): bool => in_array('position_id', $hiddenFields))
                    ->sortable(),
                TextColumn::make('specialty.name')
                    ->placeholder('No Specialty')
                    ->hidden(fn (): bool => in_array('specialty_id', $hiddenFields))
                    ->sortable(),
                TextColumn::make('unit.name')
                    ->placeholder('No Unit')
                    ->hidden(fn (): bool => in_array('unit_id', $hiddenFields))
                    ->sortable(),
                TextColumn::make('rank.name')
                    ->placeholder('No Rank')
                    ->hidden(fn (): bool => in_array('rank_id', $hiddenFields))
                    ->sortable(),
                TextColumn::make('status.name')
                    ->placeholder('No Status')
                    ->icon(fn (User $record): ?string => $record->status?->icon)
                    ->hidden(fn (): bool => in_array('status_id', $hiddenFields))
                    ->badge()
                    ->color(fn (?User $record): array => Color::generateV3Palette($record->status->color ?? '#2563eb'))
                    ->sortable(),
                TextColumn::make('roles.name')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->hidden(fn (): bool => in_array('created_at', $hiddenFields))
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->hidden(fn (): bool => in_array('updated_at', $hiddenFields))
                    ->sortable(),
            ])
            ->groups(['approved', 'position.name', 'rank.name', 'specialty.name', 'status.name', 'unit.name'])
            ->filters([
                TernaryFilter::make('approved'),
                AwardsFilter::make(),
                CredentialsFilter::make(),
                SelectFilter::make('position')
                    ->relationship('position', 'name')
                    ->preload()
                    ->multiple(),
                QualificationsFilter::make(),
                SelectFilter::make('rank')
                    ->relationship('rank', 'name')
                    ->preload()
                    ->multiple(),
                SelectFilter::make('roles.name')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->multiple(),
                SelectFilter::make('specialty')
                    ->relationship('specialty', 'name')
                    ->preload()
                    ->multiple(),
                SelectFilter::make('status')
                    ->relationship('status', 'name')
                    ->preload()
                    ->multiple(),
                SelectFilter::make('unit')
                    ->relationship('unit', 'name')
                    ->preload()
                    ->multiple(),
            ])
            ->recordActions([
                Action::make('approve')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (?User $record): bool => ! $record->approved && Auth::user()->can('approve', $record))
                    ->successNotificationTitle('The user has been successfully approved.')
                    ->action(function (Action $action, User $record): void {
                        $record->forceFill([
                            'approved' => true,
                        ])->save();

                        $action->success();
                    }),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                ExportBulkAction::make()
                    ->exporter(UserExporter::class)
                    ->icon('heroicon-o-document-arrow-down'),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
            'view' => ViewUser::route('/{record}'),
        ];
    }

    /**
     * @param  User  $record
     */
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->display_name;
    }

    /**
     * @param  User  $record
     * @return array<string, mixed>
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Position' => $record->position?->name,
            'Specialty' => $record->specialty?->name,
            'Unit' => $record->unit?->name,
            'Rank' => $record->rank?->name,
            'Status' => $record->status?->name,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }
}
