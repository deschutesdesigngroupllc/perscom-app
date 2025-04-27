<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Features\ExportDataFeature;
use App\Filament\App\Resources\UserResource\Pages;
use App\Filament\App\Resources\UserResource\RelationManagers;
use App\Filament\Exports\UserExporter;
use App\Models\User;
use App\Services\SettingsService;
use App\Services\UserSettingsService;
use App\Settings\DashboardSettings;
use App\Settings\OrganizationSettings;
use App\Traits\Filament\InteractsWithFields;
use BezhanSalleh\FilamentShield\Support\Utils;
use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Livewire;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Laravel\Pennant\Feature;

class UserResource extends BaseResource
{
    use InteractsWithFields;

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Personnel';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Demographics')
                            ->icon('heroicon-o-user')
                            ->columns()
                            ->schema([
                                Forms\Components\Grid::make(8)
                                    ->schema([
                                        Forms\Components\FileUpload::make('profile_photo')
                                            ->columnSpan(1)
                                            ->visibility('public')
                                            ->image()
                                            ->imageEditor()
                                            ->downloadable()
                                            ->openable()
                                            ->avatar()
                                            ->previewable(),
                                        Forms\Components\Grid::make(1)
                                            ->columnSpan(7)
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->required()
                                                    ->columnSpanFull()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('email')
                                                    ->unique(ignoreRecord: true)
                                                    ->email()
                                                    ->required()
                                                    ->columnSpanFull()
                                                    ->maxLength(255),
                                            ]),
                                    ]),
                                Forms\Components\FileUpload::make('cover_photo')
                                    ->columnSpanFull()
                                    ->visibility('public')
                                    ->image()
                                    ->imageEditor()
                                    ->downloadable()
                                    ->openable()
                                    ->previewable(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Assignment')
                            ->icon('heroicon-o-rectangle-stack')
                            ->columns()
                            ->schema([
                                Forms\Components\Placeholder::make('last_assignment_change_date')
                                    ->helperText('This date is only changed through a primary assignment record.')
                                    ->label('Last assignment changed')
                                    ->hiddenOn(['create', 'edit'])
                                    ->visible(fn ($state) => filled($state))
                                    ->content(fn (?User $record) => optional($record?->last_assignment_change_date, fn (CarbonInterface $date) => $date->longRelativeToNowDiffForHumans())),
                                Forms\Components\Placeholder::make('time_in_assignment')
                                    ->label('Time in assignment')
                                    ->hiddenOn(['create', 'edit'])
                                    ->visible(fn ($state) => filled($state))
                                    ->content(fn (?User $record) => optional($record?->time_in_assignment, fn ($date): string => CarbonInterval::make($date)->forHumans())),
                                Forms\Components\Select::make('position_id')
                                    ->helperText('The user\'s current position.')
                                    ->columnSpanFull()
                                    ->preload()
                                    ->relationship(name: 'position', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form): Form => PositionResource::form($form)),
                                Forms\Components\Select::make('specialty_id')
                                    ->helperText('The user\'s current specialty.')
                                    ->columnSpanFull()
                                    ->preload()
                                    ->relationship(name: 'specialty', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form): Form => SpecialtyResource::form($form)),
                                Forms\Components\Select::make('unit_id')
                                    ->helperText('The user\'s current unit.')
                                    ->columnSpanFull()
                                    ->preload()
                                    ->relationship(name: 'unit', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form): Form => UnitResource::form($form)),
                            ]),
                        Forms\Components\Tabs\Tab::make('Custom Fields')
                            ->hiddenOn('create')
                            ->icon('heroicon-o-pencil')
                            ->schema(fn (?User $record): array => UserResource::getFormSchemaFromFields($record)),
                        Forms\Components\Tabs\Tab::make('Details')
                            ->hiddenOn('create')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\DateTimePicker::make('created_at')
                                    ->default(now())
                                    ->required()
                                    ->helperText('The date the user profile was created. Used to calculate time in service.'),
                                Forms\Components\DateTimePicker::make('updated_at')
                                    ->default(now())
                                    ->required()
                                    ->helperText('The date the user profile was last updated.'),
                                Forms\Components\DateTimePicker::make('last_seen_at')
                                    ->timezone(UserSettingsService::get('timezone', function () {
                                        /** @var OrganizationSettings $settings */
                                        $settings = app(OrganizationSettings::class);

                                        return $settings->timezone ?? config('app.timezone');
                                    }))
                                    ->label('Last Online')
                                    ->helperText('The date the user last logged in.'),
                                Forms\Components\DateTimePicker::make('email_verified_at')
                                    ->timezone(UserSettingsService::get('timezone', function () {
                                        /** @var OrganizationSettings $settings */
                                        $settings = app(OrganizationSettings::class);

                                        return $settings->timezone ?? config('app.timezone');
                                    }))
                                    ->label('Email Verified')
                                    ->helperText('The date the user\'s email was verified. Set this to bypass user email verification.'),
                            ]),
                        Forms\Components\Tabs\Tab::make('Notes')
                            ->icon('heroicon-o-pencil-square')
                            ->schema([
                                Forms\Components\Placeholder::make('notes_updated_at')
                                    ->label('Notes updated')
                                    ->hiddenOn(['create', 'edit'])
                                    ->visible(fn ($state) => filled($state))
                                    ->content(fn (?User $record) => optional($record?->notes_updated_at, fn (CarbonInterface $date) => $date->longRelativeToNowDiffForHumans())),
                                Forms\Components\RichEditor::make('notes')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull()
                                    ->helperText('Use this optional area to keep notes on the user.'),
                            ]),
                        Forms\Components\Tabs\Tab::make('Rank')
                            ->icon('heroicon-o-chevron-double-up')
                            ->columns()
                            ->schema([
                                Forms\Components\Placeholder::make('last_rank_change_date')
                                    ->helperText('This date is only changed through a rank record.')
                                    ->label('Last rank changed')
                                    ->hiddenOn(['create', 'edit'])
                                    ->visible(fn ($state) => filled($state))
                                    ->content(fn (?User $record) => optional($record?->last_rank_change_date, fn (CarbonInterface $date) => $date->longRelativeToNowDiffForHumans())),
                                Forms\Components\Placeholder::make('time_in_grade')
                                    ->label('Time in grade')
                                    ->hiddenOn(['create', 'edit'])
                                    ->visible(fn ($state) => filled($state))
                                    ->content(fn (?User $record) => optional($record?->time_in_grade, fn ($date): string => CarbonInterval::make($date)->forHumans())),
                                Forms\Components\Select::make('rank_id')
                                    ->helperText('The user\'s current rank.')
                                    ->preload()
                                    ->columnSpanFull()
                                    ->relationship(name: 'rank', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form): Form => RankResource::form($form)),
                            ]),
                        Forms\Components\Tabs\Tab::make('Status')
                            ->badge(fn (?User $record) => $record?->status?->name)
                            ->badgeColor(fn (?User $record): array => Color::hex($record->status->color ?? '#2563eb'))
                            ->icon('heroicon-o-scale')
                            ->schema([
                                Forms\Components\Select::make('status_id')
                                    ->helperText('The user\'s current status.')
                                    ->preload()
                                    ->relationship(name: 'status', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form): Form => StatusResource::form($form)),
                                Forms\Components\Toggle::make('approved')
                                    ->helperText('Turn off to disable account access.')
                                    ->required()
                                    ->default(true),
                            ]),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        $hiddenFields = Arr::wrap(SettingsService::get(DashboardSettings::class, 'user_hidden_fields', []));

        return $infolist
            ->schema([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Demographics')
                            ->columnSpanFull()
                            ->badge(fn (?User $record) => in_array('status_id', $hiddenFields) ? null : $record?->status?->name)
                            ->badgeColor(fn (?User $record): array => Color::hex($record->status->color ?? '#2563eb'))
                            ->icon('heroicon-o-user')
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
                                    ->label('Last Online')
                                    ->dateTime()
                                    ->hidden(fn (): bool => in_array('last_seen_at', $hiddenFields)),
                            ]),
                        Tab::make('Assignment')
                            ->icon('heroicon-o-rectangle-stack')
                            ->schema([
                                Section::make('Primary Assignment')
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
                                        Livewire::make(RelationManagers\SecondaryAssignmentsRelationManager::class, fn (?User $record): array => [
                                            'ownerRecord' => $record,
                                            'pageClass' => Pages\ViewUser::class,
                                        ]),
                                    ]),
                            ]),
                        Tab::make('Awards')
                            ->icon('heroicon-o-trophy')
                            ->schema([
                                TextEntry::make('awards.name')
                                    ->label('List of Awards')
                                    ->listWithLineBreaks(),
                            ]),
                        Tab::make('Credentials')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                TextEntry::make('credentials.name')
                                    ->label('List of Credentials')
                                    ->listWithLineBreaks(),
                            ]),
                        Tab::make('Qualifications')
                            ->icon('heroicon-o-star')
                            ->schema([
                                TextEntry::make('qualifications.name')
                                    ->label('List of Qualifications')
                                    ->listWithLineBreaks(),
                            ]),
                        Tab::make('Rank')
                            ->icon('heroicon-o-chevron-double-up')
                            ->columns()
                            ->schema([
                                TextEntry::make('rank.name')
                                    ->label('Current Rank')
                                    ->badge()
                                    ->color('gray')
                                    ->hidden(fn (): bool => in_array('rank_id', $hiddenFields))
                                    ->prefix(fn (?User $record) => optional($record?->rank?->image?->image_url, fn ($url): HtmlString => new HtmlString("<img src='$url' class='h-5 inline' alt='{$record->rank->name}' />")))
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
            ]);
    }

    public static function table(Table $table): Table
    {
        $hiddenFields = Arr::wrap(SettingsService::get(DashboardSettings::class, 'user_hidden_fields', []));

        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('profile_photo')
                    ->hidden(fn (): bool => in_array('profile_photo', $hiddenFields))
                    ->label('')
                    ->defaultImageUrl(fn (User $record) => $record->profile_photo_url),
                Tables\Columns\TextColumn::make('name')
                    ->hidden(fn (): bool => in_array('name', $hiddenFields))
                    ->sortable()
                    ->searchable()
                    ->icon(fn (?User $record): ?string => ! $record->approved && Auth::user()->hasRole(Utils::getSuperAdminName()) ? 'heroicon-o-exclamation-circle' : null)
                    ->iconColor('danger')
                    ->iconPosition(IconPosition::After),
                Tables\Columns\TextColumn::make('email')
                    ->hidden(fn (): bool => in_array('email', $hiddenFields))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('online')
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
                Tables\Columns\TextColumn::make('position.name')
                    ->hidden(fn (): bool => in_array('position_id', $hiddenFields))
                    ->sortable(),
                Tables\Columns\TextColumn::make('specialty.name')
                    ->hidden(fn (): bool => in_array('specialty_id', $hiddenFields))
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit.name')
                    ->hidden(fn (): bool => in_array('unit_id', $hiddenFields))
                    ->sortable(),
                Tables\Columns\TextColumn::make('rank.name')
                    ->hidden(fn (): bool => in_array('rank_id', $hiddenFields))
                    ->sortable(),
                Tables\Columns\TextColumn::make('status.name')
                    ->hidden(fn (): bool => in_array('status_id', $hiddenFields))
                    ->badge()
                    ->color(fn (?User $record): array => Color::hex($record->status->color ?? '#2563eb'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->hidden(fn (): bool => in_array('created_at', $hiddenFields))
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->hidden(fn (): bool => in_array('updated_at', $hiddenFields))
                    ->sortable(),
            ])
            ->groups(['approved', 'position.name', 'rank.name', 'specialty.name', 'status.name', 'unit.name'])
            ->filters([
                Tables\Filters\TernaryFilter::make('approved'),
                Tables\Filters\SelectFilter::make('position')
                    ->relationship('position', 'name')
                    ->preload()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('specialty')
                    ->relationship('specialty', 'name')
                    ->preload()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('unit')
                    ->relationship('unit', 'name')
                    ->preload()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('rank')
                    ->relationship('rank', 'name')
                    ->preload()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('status')
                    ->relationship('status', 'name')
                    ->preload()
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (?User $record): bool => ! $record->approved && Auth::user()->hasRole(Utils::getSuperAdminName()))
                    ->successNotificationTitle('The user has been successfully approved.')
                    ->action(function (Tables\Actions\Action $action, User $record): void {
                        $record->forceFill([
                            'approved' => true,
                        ])->save();

                        $action->success();
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ExportAction::make()
                        ->visible(Feature::active(ExportDataFeature::class))
                        ->exporter(UserExporter::class),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}'),
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

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }
}
