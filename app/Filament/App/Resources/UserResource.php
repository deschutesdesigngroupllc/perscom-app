<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\UserResource\Pages\CreateUser;
use App\Filament\App\Resources\UserResource\Pages\EditUser;
use App\Filament\App\Resources\UserResource\Pages\ListUsers;
use App\Filament\App\Resources\UserResource\Pages\ViewUser;
use App\Filament\App\Resources\UserResource\RelationManagers\SecondaryAssignmentsRelationManager;
use App\Filament\Exports\UserExporter;
use App\Models\User;
use App\Services\SettingsService;
use App\Services\UserSettingsService;
use App\Settings\DashboardSettings;
use App\Settings\OrganizationSettings;
use App\Traits\Filament\InteractsWithFields;
use BackedEnum;
use BezhanSalleh\FilamentShield\Support\Utils;
use Carbon\CarbonInterval;
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
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use UnitEnum;

class UserResource extends BaseResource
{
    use InteractsWithFields;

    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|UnitEnum|null $navigationGroup = 'Personnel';

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'name';

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
                                    ->schema([
                                        FileUpload::make('profile_photo')
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
                                    ->columnSpanFull()
                                    ->visibility('public')
                                    ->image()
                                    ->imageEditor()
                                    ->downloadable()
                                    ->openable()
                                    ->previewable(),
                            ]),
                        Tab::make('Assignment')
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
                        Tab::make('Custom Fields')
                            ->hiddenOn('create')
                            ->icon('heroicon-o-pencil')
                            ->schema(fn (?User $record): array => UserResource::getFormSchemaFromFields($record)),
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
                                        $settings = app(OrganizationSettings::class);

                                        return $settings->timezone ?? config('app.timezone');
                                    }))
                                    ->label('Last Online')
                                    ->helperText('The date the user last logged in.'),
                                DateTimePicker::make('email_verified_at')
                                    ->timezone(UserSettingsService::get('timezone', function () {
                                        /** @var OrganizationSettings $settings */
                                        $settings = app(OrganizationSettings::class);

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
                Tabs::make()
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Demographics')
                            ->columnSpanFull()
                            ->badge(fn (?User $record) => in_array('status_id', $hiddenFields) ? null : $record?->status?->name)
                            ->badgeColor(fn (?User $record): array => Color::generateV3Palette($record->status->color ?? '#2563eb'))
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make('Personnel File')
                                    ->description("The user's vital statistics.")
                                    ->schema([
                                        ImageEntry::make('cover_photo_url')
                                            ->hidden(fn (User $record): bool => in_array('cover_photo', $hiddenFields) || is_null($record->cover_photo_url))
                                            ->height(function () {
                                                /** @var DashboardSettings $settings */
                                                $settings = app(DashboardSettings::class);

                                                return $settings->cover_photo_height ?? 100;
                                            })
                                            ->extraAttributes([
                                                'class' => 'user-cover-photo',
                                            ])
                                            ->columnSpanFull()
                                            ->hiddenLabel(),
                                        TextEntry::make('name')
                                            ->hidden(fn (): bool => in_array('name', $hiddenFields)),
                                        TextEntry::make('time_in_service')
                                            ->label('Time In Service')
                                            ->hidden(fn (): bool => in_array('time_in_service', $hiddenFields))
                                            ->formatStateUsing(fn ($state): string => CarbonInterval::make($state)->forHumans()),
                                        TextEntry::make('last_seen_at')
                                            ->placeholder('Unknown')
                                            ->label('Last Online')
                                            ->dateTime()
                                            ->hidden(fn (): bool => in_array('last_seen_at', $hiddenFields)),
                                    ]),
                            ]),
                        Tab::make('Assignment')
                            ->icon('heroicon-o-rectangle-stack')
                            ->schema([
                                Section::make('Primary Assignment')
                                    ->description("The user's current primary assignment.")
                                    ->columns(3)
                                    ->schema([
                                        TextEntry::make('position.name')
                                            ->label('Current Position')
                                            ->badge()
                                            ->color('gray')
                                            ->hidden(fn (): bool => in_array('position_id', $hiddenFields)),
                                        TextEntry::make('specialty.name')
                                            ->label('Current Specialty')
                                            ->badge()
                                            ->color('gray')
                                            ->hidden(fn (): bool => in_array('specialty_id', $hiddenFields)),
                                        TextEntry::make('unit.name')
                                            ->label('Current Unit')
                                            ->badge()
                                            ->color('gray')
                                            ->hidden(fn (): bool => in_array('unit_id', $hiddenFields)),
                                        TextEntry::make('time_in_assignment')
                                            ->label('Time In Assignment')
                                            ->hidden(fn (User $record): bool => in_array('time_in_assignment', $hiddenFields) || is_null($record->time_in_assignment))
                                            ->formatStateUsing(fn ($state): string => CarbonInterval::make($state)->forHumans())
                                            ->columnSpanFull(),
                                        TextEntry::make('last_assignment_change_date')
                                            ->label('Assignment Last Changed')
                                            ->dateTime()
                                            ->hidden(fn (User $record): bool => in_array('last_assignment_change_date', $hiddenFields) || is_null($record->last_assignment_change_date))
                                            ->columnSpanFull(),
                                    ]),
                                Section::make()
                                    ->hidden(fn (): bool => in_array('secondary_assignment_records', $hiddenFields))
                                    ->schema([
                                        Livewire::make(SecondaryAssignmentsRelationManager::class, fn (?User $record): array => [
                                            'ownerRecord' => $record,
                                            'pageClass' => ViewUser::class,
                                        ]),
                                    ]),
                            ]),
                        Tab::make('Awards')
                            ->icon('heroicon-o-trophy')
                            ->schema([
                                Section::make('Awards')
                                    ->description('The awards the user has received.')
                                    ->schema([
                                        TextEntry::make('awards.name')
                                            ->placeholder('No Awards Available')
                                            ->hiddenLabel()
                                            ->listWithLineBreaks(),
                                    ]),
                            ]),
                        Tab::make('Credentials')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                Section::make('Credentials')
                                    ->description('The credentials the user has assigned.')
                                    ->schema([
                                        TextEntry::make('credentials.name')
                                            ->placeholder('No Credentials Available')
                                            ->hiddenLabel()
                                            ->listWithLineBreaks(),
                                    ]),
                            ]),
                        Tab::make('Qualifications')
                            ->icon('heroicon-o-star')
                            ->schema([
                                Section::make('Qualifications')
                                    ->description('The qualifications the user has earned.')
                                    ->schema([
                                        TextEntry::make('qualifications.name')
                                            ->placeholder('No Qualifications Available')
                                            ->hiddenLabel()
                                            ->listWithLineBreaks(),
                                    ]),
                            ]),
                        Tab::make('Rank')
                            ->icon('heroicon-o-chevron-double-up')
                            ->columns()
                            ->schema([
                                Section::make('Current Rank')
                                    ->description("The user's current rank.")
                                    ->columns(3)
                                    ->schema([
                                        TextEntry::make('rank.name')
                                            ->hiddenLabel()
                                            ->badge()
                                            ->color('gray')
                                            ->hidden(fn (): bool => in_array('rank_id', $hiddenFields))
                                            ->prefix(fn (?User $record) => optional($record?->rank?->image?->image_url, fn (string $url): HtmlString => new HtmlString(sprintf("<img src='%s' class='h-5 inline' alt='%s' />", $url, $record->rank->name))))
                                            ->columnSpanFull(),
                                        TextEntry::make('time_in_grade')
                                            ->label('Time In Grade')
                                            ->dateTime()
                                            ->hidden(fn (User $record): bool => in_array('time_in_grade', $hiddenFields) || is_null($record->time_in_grade))
                                            ->formatStateUsing(fn ($state): string => CarbonInterval::make($state)->forHumans())
                                            ->columnSpanFull(),
                                        TextEntry::make('last_rank_change_date')
                                            ->label('Rank Last Changed')
                                            ->dateTime()
                                            ->hidden(fn (User $record): bool => in_array('last_rank_change_date', $hiddenFields) || is_null($record->last_rank_change_date))
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $hiddenFields = Arr::wrap(SettingsService::get(DashboardSettings::class, 'user_hidden_fields', []));

        return $table
            ->emptyStateDescription('There are no users to view. Create one to get started.')
            ->columns([
                ImageColumn::make('profile_photo')
                    ->placeholder('No Profile Photo')
                    ->hidden(fn (): bool => in_array('profile_photo', $hiddenFields))
                    ->label('')
                    ->defaultImageUrl(fn (User $record) => $record->profile_photo_url),
                TextColumn::make('name')
                    ->hidden(fn (): bool => in_array('name', $hiddenFields))
                    ->sortable()
                    ->searchable()
                    ->icon(fn (?User $record): ?string => ! $record->approved && Auth::user()->hasRole(Utils::getSuperAdminName()) ? 'heroicon-o-exclamation-circle' : null)
                    ->iconColor('danger')
                    ->iconPosition(IconPosition::After),
                TextColumn::make('email')
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
                    ->hidden(fn (): bool => in_array('position_id', $hiddenFields))
                    ->sortable(),
                TextColumn::make('specialty.name')
                    ->hidden(fn (): bool => in_array('specialty_id', $hiddenFields))
                    ->sortable(),
                TextColumn::make('unit.name')
                    ->hidden(fn (): bool => in_array('unit_id', $hiddenFields))
                    ->sortable(),
                TextColumn::make('rank.name')
                    ->hidden(fn (): bool => in_array('rank_id', $hiddenFields))
                    ->sortable(),
                TextColumn::make('status.name')
                    ->icon(fn (User $record): ?string => $record->status?->icon)
                    ->hidden(fn (): bool => in_array('status_id', $hiddenFields))
                    ->badge()
                    ->color(fn (?User $record): array => Color::generateV3Palette($record->status->color ?? '#2563eb'))
                    ->sortable(),
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
                SelectFilter::make('position')
                    ->relationship('position', 'name')
                    ->preload()
                    ->multiple(),
                SelectFilter::make('specialty')
                    ->relationship('specialty', 'name')
                    ->preload()
                    ->multiple(),
                SelectFilter::make('unit')
                    ->relationship('unit', 'name')
                    ->preload()
                    ->multiple(),
                SelectFilter::make('rank')
                    ->relationship('rank', 'name')
                    ->preload()
                    ->multiple(),
                SelectFilter::make('status')
                    ->relationship('status', 'name')
                    ->preload()
                    ->multiple(),
            ])
            ->recordActions([
                Action::make('approve')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (?User $record): bool => ! $record->approved && Auth::user()->hasRole(Utils::getSuperAdminName()))
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
        return $record->name;
    }

    /**
     * @param  User  $record
     * @return array<string, mixed>
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Position' => optional($record->position)->name,
            'Specialty' => optional($record->specialty)->name,
            'Unit' => optional($record->unit)->name,
            'Rank' => optional($record->rank)->name,
            'Status' => optional($record->status)->name,
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
