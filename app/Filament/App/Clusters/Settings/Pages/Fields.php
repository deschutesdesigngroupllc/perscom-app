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
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-pencil';

    protected static string|UnitEnum|null $navigationGroup = 'Dashboard';

    protected static string $settings = FieldSettings::class;

    protected static ?string $cluster = Settings::class;

    protected ?string $subheading = 'Manage your default field settings.';

    protected static ?string $slug = 'dashboard/fields';

    protected static ?int $navigationSort = 1;

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
                            ->icon('heroicon-o-rectangle-stack')
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
                            ->icon('heroicon-o-trophy')
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
                            ->icon('heroicon-o-fire')
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
                            ->icon('heroicon-o-star')
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
                            ->icon('heroicon-o-chevron-double-up')
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
                            ->icon('heroicon-o-clipboard-document-list')
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
                            ->icon('heroicon-o-academic-cap')
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
                            ->icon('heroicon-o-users')
                            ->schema([
                                Select::make('users')
                                    ->label('Custom Fields')
                                    ->options(Field::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload()
                                    ->helperText('Add the specified custom fields to user profiles.')
                                    ->hintAction(
                                        Action::make('syncUsers')
                                            ->label('Sync All Users')
                                            ->icon(Heroicon::OutlinedArrowPath)
                                            ->requiresConfirmation()
                                            ->modalHeading('Sync Custom Fields to All Users')
                                            ->modalDescription('This will sync the custom field relationships for all existing users.')
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
            ->title('Users synced successfully')
            ->body(sprintf('%d users were synced with the custom field configuration.', $usersUpdated))
            ->success()
            ->send();
    }
}
