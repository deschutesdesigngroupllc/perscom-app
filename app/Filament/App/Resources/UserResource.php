<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Features\ExportDataFeature;
use App\Filament\App\Resources\UserResource\Pages;
use App\Filament\App\Resources\UserResource\RelationManagers;
use App\Filament\Exports\UserExporter;
use App\Models\User;
use App\Settings\DashboardSettings;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
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
                                            ->disk('s3')
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
                                    ->disk('s3')
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
                                    ->content(fn (?User $record) => optional($record?->time_in_assignment, fn ($date) => CarbonInterval::make($date)->forHumans())),
                                Forms\Components\Select::make('position_id')
                                    ->helperText('The user\'s current position.')
                                    ->columnSpanFull()
                                    ->preload()
                                    ->relationship(name: 'position', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form) => PositionResource::form($form)),
                                Forms\Components\Select::make('specialty_id')
                                    ->helperText('The user\'s current specialty.')
                                    ->columnSpanFull()
                                    ->preload()
                                    ->relationship(name: 'specialty', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form) => SpecialtyResource::form($form)),
                                Forms\Components\Select::make('unit_id')
                                    ->helperText('The user\'s current unit.')
                                    ->columnSpanFull()
                                    ->preload()
                                    ->relationship(name: 'unit', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form) => UnitResource::form($form)),
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
                                    ->content(fn (?User $record) => optional($record?->time_in_grade, fn ($date) => CarbonInterval::make($date)->forHumans())),
                                Forms\Components\Select::make('rank_id')
                                    ->helperText('The user\'s current rank.')
                                    ->preload()
                                    ->columnSpanFull()
                                    ->relationship(name: 'rank', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form) => RankResource::form($form)),
                            ]),
                        Forms\Components\Tabs\Tab::make('Status')
                            ->badge(fn (?User $record) => $record?->status?->name)
                            ->badgeColor(fn (?User $record) => Color::hex($record->status->color ?? '#2563eb'))
                            ->icon('heroicon-o-scale')
                            ->schema([
                                Forms\Components\Select::make('status_id')
                                    ->helperText('The user\'s current status.')
                                    ->preload()
                                    ->relationship(name: 'status', titleAttribute: 'name')
                                    ->searchable()
                                    ->createOptionForm(fn ($form) => StatusResource::form($form)),
                                Forms\Components\Toggle::make('approved')
                                    ->helperText('Turn off to disable account access.')
                                    ->required()
                                    ->default(true),
                            ]),
                        Forms\Components\Tabs\Tab::make('Custom Fields')
                            ->hiddenOn('create')
                            ->icon('heroicon-o-pencil')
                            ->schema(fn (?User $record) => UserResource::getFormSchemaFromFields($record)),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Demographics')
                            ->columnSpanFull()
                            ->badge(fn (?User $record) => $record?->status?->name)
                            ->badgeColor(fn (?User $record) => Color::hex($record->status->color ?? '#2563eb'))
                            ->icon('heroicon-o-user')
                            ->schema([
                                ImageEntry::make('cover_photo_url')
                                    ->height(function () {
                                        /** @var DashboardSettings $settings */
                                        $settings = app(DashboardSettings::class);

                                        return $settings->cover_photo_height ?? 100;
                                    })
                                    ->extraAttributes([
                                        'class' => 'user-cover-photo',
                                    ])
                                    ->columnSpanFull()
                                    ->hiddenLabel()
                                    ->hidden(fn (?User $record) => is_null($record->cover_photo_url)),
                                TextEntry::make('name'),
                                TextEntry::make('time_in_service')
                                    ->formatStateUsing(fn ($state) => CarbonInterval::make($state)->forHumans()),
                            ]),
                        Tab::make('Assignment')
                            ->icon('heroicon-o-rectangle-stack')
                            ->schema([
                                Section::make('Primary Assignment')
                                    ->columns(3)
                                    ->schema([
                                        TextEntry::make('position.name'),
                                        TextEntry::make('specialty.name'),
                                        TextEntry::make('unit.name'),
                                        TextEntry::make('last_assignment_change_date')
                                            ->formatStateUsing(fn ($state) => $state->longRelativeToNowDiffForHumans())
                                            ->hidden(fn (?User $record) => is_null($record->last_assignment_change_date)),
                                        TextEntry::make('time_in_assignment')
                                            ->formatStateUsing(fn ($state) => CarbonInterval::make($state)->forHumans())
                                            ->hidden(fn (?User $record) => is_null($record->time_in_assignment)),
                                    ]),
                                Section::make('Secondary Assignment(s)')
                                    ->schema([
                                        Livewire::make(RelationManagers\SecondaryAssignmentsRelationManager::class, fn (?User $record) => [
                                            'ownerRecord' => $record,
                                            'pageClass' => Pages\ViewUser::class,
                                        ]),
                                    ]),
                            ]),
                        Tab::make('Rank')
                            ->icon('heroicon-o-chevron-double-up')
                            ->columns()
                            ->schema([
                                TextEntry::make('rank.name')
                                    ->prefix(fn (?User $record) => optional($record?->rank?->image?->image_url, fn ($url) => new HtmlString("<img src='$url' class='h-5 inline' alt='{$record->rank->name}' />")))
                                    ->columnSpanFull(),
                                TextEntry::make('last_rank_change_date')
                                    ->formatStateUsing(fn ($state) => $state->longRelativeToNowDiffForHumans())
                                    ->hidden(fn (?User $record) => is_null($record->last_rank_change_date)),
                                TextEntry::make('time_in_grade')
                                    ->formatStateUsing(fn ($state) => CarbonInterval::make($state)->forHumans())
                                    ->hidden(fn (?User $record) => is_null($record->time_in_grade)),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('profile_photo')
                    ->label('')
                    ->defaultImageUrl(fn (User $record) => $record->profile_photo_url)
                    ->disk('s3'),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->icon(fn (?User $record) => ! $record->approved && Auth::user()->hasRole(Utils::getSuperAdminName()) ? 'heroicon-o-exclamation-circle' : null)
                    ->iconColor('danger')
                    ->iconPosition(IconPosition::After),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('online')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        true => 'success',
                        default => 'info',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        true => 'Online',
                        default => 'Offline',
                    }),
                Tables\Columns\TextColumn::make('position.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('specialty.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('rank.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status.name')
                    ->badge()
                    ->color(fn (?User $record) => Color::hex($record->status->color ?? '#2563eb'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_at')
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
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (?User $record) => ! $record->approved && Auth::user()->hasRole(Utils::getSuperAdminName()))
                    ->successNotificationTitle('The user has been successfully approved.')
                    ->action(function (Tables\Actions\Action $action, User $record) {
                        $record->forceFill([
                            'approved' => true,
                        ])->save();

                        $action->success();
                    }),
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
                        ->exporter(UserExporter::class),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }

    public static function getGlobalSearchResultTitle(Model|User $record): string
    {
        return $record->name;
    }

    public static function getGlobalSearchResultDetails(Model|User $record): array
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
