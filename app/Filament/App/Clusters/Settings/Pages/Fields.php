<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Settings\Pages;

use App\Filament\App\Clusters\Settings;
use App\Models\Field;
use App\Models\User;
use App\Settings\FieldSettings;
use BackedEnum;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class Fields extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static ?string $navigationLabel = 'Field Defaults';

    protected static string|UnitEnum|null $navigationGroup = 'Data & Files';

    protected static string $settings = FieldSettings::class;

    protected static ?string $cluster = Settings::class;

    protected ?string $subheading = 'Manage your default field settings.';

    protected static ?string $slug = 'dashboard/fields';

    protected static ?int $navigationSort = 31;

    public static function canAccess(): bool
    {
        return parent::canAccess()
            && Auth::user()->hasRole(Utils::getSuperAdminName())
            && ! App::isDemo();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Assignment Records')
                            ->icon(Heroicon::OutlinedRectangleStack)
                            ->schema([
                                Select::make('assignment_records')
                                    ->label('Custom Fields')
                                    ->options(Field::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload()
                                    ->helperText('Add the specified custom fields to the assignment record form.'),
                            ]),
                        Tab::make('Award Records')
                            ->icon(Heroicon::OutlinedTrophy)
                            ->schema([
                                Select::make('award_records')
                                    ->label('Custom Fields')
                                    ->options(Field::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload()
                                    ->helperText('Add the specified custom fields to the assignment record form.'),
                            ]),
                        Tab::make('Combat Records')
                            ->icon(Heroicon::OutlinedFire)
                            ->schema([
                                Select::make('combat_records')
                                    ->label('Custom Fields')
                                    ->options(Field::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload()
                                    ->helperText('Add the specified custom fields to the assignment record form.'),
                            ]),
                        Tab::make('Qualification Records')
                            ->icon(Heroicon::OutlinedStar)
                            ->schema([
                                Select::make('qualification_records')
                                    ->label('Custom Fields')
                                    ->options(Field::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload()
                                    ->helperText('Add the specified custom fields to the assignment record form.'),
                            ]),
                        Tab::make('Rank Records')
                            ->icon(Heroicon::OutlinedChevronDoubleUp)
                            ->schema([
                                Select::make('rank_records')
                                    ->label('Custom Fields')
                                    ->options(Field::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload()
                                    ->helperText('Add the specified custom fields to the assignment record form.'),
                            ]),
                        Tab::make('Service Records')
                            ->icon(Heroicon::OutlinedClipboardDocumentList)
                            ->schema([
                                Select::make('service_records')
                                    ->label('Custom Fields')
                                    ->options(Field::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload()
                                    ->helperText('Add the specified custom fields to the assignment record form.'),
                            ]),
                        Tab::make('Training Records')
                            ->icon(Heroicon::OutlinedAcademicCap)
                            ->schema([
                                Select::make('training_records')
                                    ->label('Custom Fields')
                                    ->options(Field::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload()
                                    ->helperText('Add the specified custom fields to the assignment record form.'),
                            ]),
                        Tab::make('Users')
                            ->icon(Heroicon::OutlinedUsers)
                            ->schema([
                                Select::make('users')
                                    ->label('Custom Fields')
                                    ->options(Field::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload()
                                    ->helperText('Add the specified custom fields to member profiles.')
                                    ->hintAction(
                                        Action::make('syncUsers')
                                            ->label('Sync All Members')
                                            ->icon(Heroicon::OutlinedArrowPath)
                                            ->requiresConfirmation()
                                            ->modalHeading('Sync Custom Fields to All Members')
                                            ->modalDescription('This will sync the custom field relationships for all existing members.')
                                            ->form([
                                                Radio::make('sync_mode')
                                                    ->label('How should existing fields be handled?')
                                                    ->options([
                                                        'with_detaching' => 'Replace all fields (removes fields not in list)',
                                                        'without_detaching' => 'Add only (keeps existing fields, adds new ones)',
                                                    ])
                                                    ->default('with_detaching')
                                                    ->required(),
                                            ])
                                            ->action(function (array $data, $state): void {
                                                $this->syncUsersWithCustomFields(
                                                    selectedFieldIds: $state ?? [],
                                                    detach: $data['sync_mode'] === 'with_detaching'
                                                );
                                            })
                                    ),
                            ]),
                    ]),
            ]);
    }

    /**
     * Sync all users with the selected custom fields via the fields relationship.
     *
     * @param  array<int, int|string>  $selectedFieldIds
     */
    protected function syncUsersWithCustomFields(array $selectedFieldIds, bool $detach): void
    {
        $usersUpdated = 0;

        User::query()->chunk(100, function ($users) use ($selectedFieldIds, $detach, &$usersUpdated): void {
            foreach ($users as $user) {
                /** @var User $user */
                if ($detach) {
                    $user->fields()->sync($selectedFieldIds);
                } else {
                    $user->fields()->syncWithoutDetaching($selectedFieldIds);
                }

                $usersUpdated++;
            }
        });

        Notification::make()
            ->title('Members Updated')
            ->body(sprintf('%d users were synced with the custom field configuration.', $usersUpdated))
            ->success()
            ->send();
    }
}
