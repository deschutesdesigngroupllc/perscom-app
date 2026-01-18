<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Filament\Actions\CopyAction;
use App\Models\Group;
use App\Models\Position;
use App\Models\Rank;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use App\Settings\OnboardingSettings;
use BackedEnum;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class Dashboard extends BaseDashboard
{
    public $defaultAction = 'onboarding';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'PERSCOM Personnel Management System';

    protected ?string $subheading = 'Personnel management made easy for high-performing, results-driven organizations.';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';

    public function onboardingAction(): Action
    {
        /** @var ?Tenant $tenant */
        $tenant = tenant();

        return Action::make('onboarding')
            ->modalHeading('Welcome to '.($tenant?->name ?? 'PERSCOM'))
            ->modalDescription("Let's get your organization set up. Complete each step below to create your organizational structure.")
            ->modalWidth(Width::SixExtraLarge)
            ->modalIcon(Heroicon::OutlinedRocketLaunch)
            ->visible(fn (): bool => $this->shouldShowOnboarding())
            ->fillForm(fn (): array => $this->getOnboardingFormData())
            ->steps($this->getOnboardingSteps())
            ->skippableSteps()
            ->modalSubmitActionLabel('Complete Setup')
            ->modalCancelActionLabel('Skip For Now')
            ->action(function (array $data): void {
                $this->completeOnboarding($data);
            })
            ->closeModalByClickingAway(false);
    }

    /**
     * @return CopyAction[]
     */
    protected function getActions(): array
    {
        return [
            CopyAction::make('perscom_id')
                ->visible(fn () => config('tenancy.enabled'))
                ->label(fn (): string => 'PERSCOM ID: '.tenant('id'))
                ->copyable((string) tenant('id'))
                ->icon('heroicon-o-document-duplicate')
                ->iconPosition(IconPosition::After)
                ->color('gray'),
        ];
    }

    protected function shouldShowOnboarding(): bool
    {
        /** @var ?User $user */
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        if (! $user->hasRole(Utils::getSuperAdminName())) {
            return false;
        }

        /** @var OnboardingSettings $settings */
        $settings = app(OnboardingSettings::class);

        return $settings->isAccessible();
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOnboardingFormData(): array
    {
        $existingGroup = Group::query()->first();
        $existingUnit = Unit::query()->first();
        $existingPosition = Position::query()->first();
        $existingRank = Rank::query()->first();
        $existingStatus = Status::query()->first();

        return [
            'group' => [
                'name' => $existingGroup?->name,
                'description' => $existingGroup?->description,
            ],
            'unit' => [
                'name' => $existingUnit?->name,
                'description' => $existingUnit?->description,
            ],
            'position' => [
                'name' => $existingPosition?->name,
                'description' => $existingPosition?->description,
            ],
            'rank' => [
                'name' => $existingRank?->name,
                'abbreviation' => $existingRank?->abbreviation,
                'paygrade' => $existingRank?->paygrade,
            ],
            'status' => [
                'name' => $existingStatus?->name,
                'color' => $existingStatus?->color,
            ],
            'personnel' => [
                'name' => null,
                'email' => null,
                'assign_unit' => $existingUnit?->id,
                'assign_position' => $existingPosition?->id,
                'assign_rank' => $existingRank?->id,
                'assign_status' => $existingStatus?->id,
            ],
            'finish' => [
                'dont_show_again' => true,
            ],
        ];
    }

    /**
     * @return Step[]
     */
    protected function getOnboardingSteps(): array
    {
        return [
            Step::make('Group')
                ->description('Create your first organizational group')
                ->icon(Heroicon::OutlinedRectangleGroup)
                ->completedIcon(Heroicon::CheckCircle)
                ->schema([
                    Section::make('Create a Group')
                        ->description('Groups are top-level divisions in your organization hierarchy (e.g., departments, divisions, or branches). Units are organized within groups.')
                        ->schema([
                            Text::make('group_info')
                                ->extraAttributes(['class' => 'w-full'])
                                ->content(new HtmlString('<div class="text-sm text-gray-500 dark:text-gray-400 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg"><strong>Organization Hierarchy:</strong> Groups → Units → Personnel<br>Groups contain units, and personnel are assigned to units. A single unit can belong to multiple groups if needed.</div>')),
                            TextInput::make('group.name')
                                ->label('Group Name')
                                ->placeholder('e.g., Infantry Division, Administrative Branch')
                                ->maxLength(255),
                            TextInput::make('group.description')
                                ->label('Description')
                                ->placeholder('A brief description of this group')
                                ->maxLength(65535),
                        ]),
                ]),
            Step::make('Unit')
                ->description('Create your first unit')
                ->icon(Heroicon::OutlinedHome)
                ->completedIcon(Heroicon::CheckCircle)
                ->schema([
                    Section::make('Create a Unit')
                        ->description('Units are subdivisions within groups where personnel are assigned (e.g., teams, squads, platoons, or stations).')
                        ->schema([
                            Text::make('unit_info')
                                ->extraAttributes(['class' => 'w-full'])
                                ->content(new HtmlString('<div class="text-sm text-gray-500 dark:text-gray-400 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">Units belong to groups and contain your personnel. Personnel are assigned to a unit, and the roster displays them organized by their unit assignment.</div>')),
                            TextInput::make('unit.name')
                                ->label('Unit Name')
                                ->placeholder('e.g., Alpha Company, Support Team, HQ Staff')
                                ->maxLength(255),
                            TextInput::make('unit.description')
                                ->label('Description')
                                ->placeholder('A brief description of this unit')
                                ->maxLength(65535),
                        ]),
                ]),
            Step::make('Position')
                ->description('Create your first position')
                ->icon(Heroicon::OutlinedIdentification)
                ->completedIcon(Heroicon::CheckCircle)
                ->schema([
                    Section::make('Create a Position')
                        ->description('Positions represent roles or job titles that are assigned directly to personnel, independent of their unit.')
                        ->schema([
                            Text::make('position_info')
                                ->extraAttributes(['class' => 'w-full'])
                                ->content(new HtmlString('<div class="text-sm text-gray-500 dark:text-gray-400 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">Positions are assigned directly to users and can be used across any unit. They affect roster sorting and help define what role each person holds (e.g., Commander, Team Leader, Medic).</div>')),
                            TextInput::make('position.name')
                                ->label('Position Name')
                                ->placeholder('e.g., Commander, Team Leader, Rifleman')
                                ->maxLength(255),
                            TextInput::make('position.description')
                                ->label('Description')
                                ->placeholder('A brief description of this position')
                                ->maxLength(65535),
                        ]),
                ]),
            Step::make('Rank')
                ->description('Create your first rank')
                ->icon(Heroicon::OutlinedChevronDoubleUp)
                ->completedIcon(Heroicon::CheckCircle)
                ->schema([
                    Section::make('Create a Rank')
                        ->description('Ranks represent seniority levels and are used for roster sorting and hierarchy display.')
                        ->schema([
                            Text::make('rank_info')
                                ->extraAttributes(['class' => 'w-full'])
                                ->content(new HtmlString('<div class="text-sm text-gray-500 dark:text-gray-400 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">Ranks are assigned directly to users and determine their seniority. They include an order value for sorting (lower = higher rank), and can have images, abbreviations, and paygrades.</div>')),
                            TextInput::make('rank.name')
                                ->label('Rank Name')
                                ->placeholder('e.g., Private, Sergeant, Captain')
                                ->maxLength(255),
                            TextInput::make('rank.abbreviation')
                                ->label('Abbreviation')
                                ->placeholder('e.g., PVT, SGT, CPT')
                                ->maxLength(255),
                            TextInput::make('rank.paygrade')
                                ->label('Paygrade')
                                ->placeholder('e.g., E-1, E-5, O-3')
                                ->maxLength(255),
                        ]),
                ]),
            Step::make('Status')
                ->description('Create your first status')
                ->icon(Heroicon::OutlinedScale)
                ->completedIcon(Heroicon::CheckCircle)
                ->schema([
                    Section::make('Create a Status')
                        ->description('Statuses indicate the current state or availability of personnel (e.g., Active, On Leave, Retired).')
                        ->schema([
                            Text::make('status_info')
                                ->extraAttributes(['class' => 'w-full'])
                                ->content(new HtmlString('<div class="text-sm text-gray-500 dark:text-gray-400 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">Statuses are assigned to personnel to indicate their current state. They can be used to filter the roster, track availability, and manage personnel lifecycle (e.g., Active Duty, Reserve, Discharged).</div>')),
                            TextInput::make('status.name')
                                ->label('Status Name')
                                ->placeholder('e.g., Active, On Leave, Retired')
                                ->maxLength(255),
                            ColorPicker::make('status.color')
                                ->label('Color'),
                        ]),
                ]),
            Step::make('Personnel')
                ->description('Create your first personnel record')
                ->icon(Heroicon::OutlinedUser)
                ->completedIcon(Heroicon::CheckCircle)
                ->schema([
                    Section::make('Create Personnel')
                        ->description('Personnel are the users in your organization. Each person is assigned to a unit, and given a position and rank.')
                        ->schema([
                            Text::make('personnel_info')
                                ->extraAttributes(['class' => 'w-full'])
                                ->content(new HtmlString('<div class="text-sm text-gray-500 dark:text-gray-400 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg"><strong>Putting it together:</strong> Personnel belong to a <strong>Unit</strong> (which is in a <strong>Group</strong>), and are assigned a <strong>Position</strong> (their role) and <strong>Rank</strong> (their seniority). The roster displays personnel organized by these assignments.</div>')),
                            TextInput::make('personnel.name')
                                ->label('Full Name')
                                ->placeholder('e.g., John Smith')
                                ->maxLength(255),
                            TextInput::make('personnel.email')
                                ->label('Email Address')
                                ->email()
                                ->placeholder('e.g., john.smith@example.com')
                                ->maxLength(255),
                            Select::make('personnel.assign_unit')
                                ->label('Assign to Unit')
                                ->helperText('Leave empty to assign the unit you created in the previous step.')
                                ->options(fn () => Unit::query()->pluck('name', 'id'))
                                ->placeholder('Select a unit or leave empty')
                                ->searchable()
                                ->preload(),
                            Select::make('personnel.assign_position')
                                ->label('Assign Position')
                                ->helperText('Leave empty to assign the position you created in the previous step.')
                                ->options(fn () => Position::query()->pluck('name', 'id'))
                                ->placeholder('Select a position or leave empty')
                                ->searchable()
                                ->preload(),
                            Select::make('personnel.assign_rank')
                                ->label('Assign Rank')
                                ->helperText('Leave empty to assign the rank you created in the previous step.')
                                ->options(fn () => Rank::query()->pluck('name', 'id'))
                                ->placeholder('Select a rank or leave empty')
                                ->searchable()
                                ->preload(),
                            Select::make('personnel.assign_status')
                                ->label('Assign Status')
                                ->helperText('Leave empty to assign the status you created in the previous step.')
                                ->options(fn () => Status::query()->pluck('name', 'id'))
                                ->placeholder('Select a status or leave empty')
                                ->searchable()
                                ->preload(),
                        ]),
                ]),
            Step::make('Documentation')
                ->description('Resources to help you get started')
                ->icon(Heroicon::OutlinedBookOpen)
                ->completedIcon(Heroicon::CheckCircle)
                ->schema([
                    Section::make('Helpful Resources')
                        ->description('Here are some resources to help you make the most of PERSCOM.')
                        ->schema([
                            Text::make('documentation_links')
                                ->content(new HtmlString('
                                    <div class="space-y-4">
                                        <div class="flex items-start gap-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-primary-500 mt-0.5 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" /></svg>
                                            <div>
                                                <a href="https://docs.perscom.io/guides/organization-structure" target="_blank" class="font-medium text-primary-600 hover:underline dark:text-primary-400">Organization Structure Guide</a>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Learn how groups, units, positions, and ranks work together.</p>
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-primary-500 mt-0.5 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>
                                            <div>
                                                <a href="https://docs.perscom.io" target="_blank" class="font-medium text-primary-600 hover:underline dark:text-primary-400">Full Documentation</a>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Explore all features and configuration options.</p>
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-primary-500 mt-0.5 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" /></svg>
                                            <div>
                                                <a href="https://docs.perscom.io/docs/support" target="_blank" class="font-medium text-primary-600 hover:underline dark:text-primary-400">Get Support</a>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Contact us if you need help or have questions.</p>
                                            </div>
                                        </div>
                                    </div>
                                ')),
                        ]),
                ]),
            Step::make('Finish')
                ->description('Complete your setup')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->completedIcon(Heroicon::CheckCircle)
                ->schema([
                    Section::make("You're all set!")
                        ->description('Click "Complete Setup" to finish and start using PERSCOM.')
                        ->schema([
                            Checkbox::make('finish.dont_show_again')
                                ->label("Don't show this setup wizard again")
                                ->helperText('You can always access organization settings from the settings menu.'),
                        ]),
                ]),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function completeOnboarding(array $data): void
    {
        $group = $this->createGroupIfNeeded($data);
        $unit = $this->createUnitIfNeeded($data, $group);
        $position = $this->createPositionIfNeeded($data);
        $rank = $this->createRankIfNeeded($data);
        $status = $this->createStatusIfNeeded($data);
        $this->createPersonnelIfNeeded($data, $unit, $position, $rank, $status);

        /** @var OnboardingSettings $settings */
        $settings = app(OnboardingSettings::class);

        if (data_get($data, 'finish.dont_show_again', false)) {
            $settings->markCompleted();
        } else {
            $settings->markDismissed();
        }

        Notification::make()
            ->success()
            ->title('Setup complete!')
            ->body('Your organization has been configured successfully.')
            ->send();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function createGroupIfNeeded(array $data): ?Group
    {
        $name = data_get($data, 'group.name');

        if (blank($name)) {
            return Group::query()->first();
        }

        $existingGroup = Group::query()->where('name', $name)->first();

        if ($existingGroup) {
            return $existingGroup;
        }

        return Group::create([
            'name' => $name,
            'description' => data_get($data, 'group.description'),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function createUnitIfNeeded(array $data, ?Group $group): ?Unit
    {
        $name = data_get($data, 'unit.name');

        if (blank($name)) {
            return Unit::query()->first();
        }

        $existingUnit = Unit::query()->where('name', $name)->first();

        if ($existingUnit) {
            if ($group && ! $existingUnit->groups->contains($group->id)) {
                $existingUnit->groups()->attach($group->id);
            }

            return $existingUnit;
        }

        $unit = Unit::create([
            'name' => $name,
            'description' => data_get($data, 'unit.description'),
        ]);

        if ($group) {
            $unit->groups()->attach($group->id);
        }

        return $unit;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function createPositionIfNeeded(array $data): ?Position
    {
        $name = data_get($data, 'position.name');

        if (blank($name)) {
            return Position::query()->first();
        }

        $existingPosition = Position::query()->where('name', $name)->first();

        if ($existingPosition) {
            return $existingPosition;
        }

        return Position::create([
            'name' => $name,
            'description' => data_get($data, 'position.description'),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function createRankIfNeeded(array $data): ?Rank
    {
        $name = data_get($data, 'rank.name');

        if (blank($name)) {
            return Rank::query()->first();
        }

        $existingRank = Rank::query()->where('name', $name)->first();

        if ($existingRank) {
            return $existingRank;
        }

        return Rank::create([
            'name' => $name,
            'abbreviation' => data_get($data, 'rank.abbreviation'),
            'paygrade' => data_get($data, 'rank.paygrade'),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function createStatusIfNeeded(array $data): ?Status
    {
        $name = data_get($data, 'status.name');

        if (blank($name)) {
            return Status::query()->first();
        }

        $existingStatus = Status::query()->where('name', $name)->first();

        if ($existingStatus) {
            return $existingStatus;
        }

        return Status::create([
            'name' => $name,
            'color' => data_get($data, 'status.color'),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function createPersonnelIfNeeded(array $data, ?Unit $unit, ?Position $position, ?Rank $rank, ?Status $status): ?User
    {
        $name = data_get($data, 'personnel.name');
        $email = data_get($data, 'personnel.email');

        if (blank($name) || blank($email)) {
            return null;
        }

        $existingUser = User::query()->where('email', $email)->first();

        if ($existingUser) {
            return $existingUser;
        }

        $assignUnitId = data_get($data, 'personnel.assign_unit') ?? $unit?->id;
        $assignPositionId = data_get($data, 'personnel.assign_position') ?? $position?->id;
        $assignRankId = data_get($data, 'personnel.assign_rank') ?? $rank?->id;
        $assignStatusId = data_get($data, 'personnel.assign_status') ?? $status?->id;

        return User::create([
            'name' => $name,
            'email' => $email,
            'approved' => true,
            'unit_id' => $assignUnitId,
            'position_id' => $assignPositionId,
            'rank_id' => $assignRankId,
            'status_id' => $assignStatusId,
        ]);
    }
}
