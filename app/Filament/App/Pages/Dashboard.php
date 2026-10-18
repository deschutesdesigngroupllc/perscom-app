<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Actions\Organization\ApplyOrganizationBlueprint;
use App\Data\Organization\OrganizationBlueprintData;
use App\Filament\Actions\CopyAction;
use App\Filament\Concerns\GeneratesOrganizationBlueprints;
use App\Forms\Components\OrganizationBlueprint;
use App\Models\Position;
use App\Models\Rank;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use App\Services\Ai\OrganizationBlueprintService;
use App\Services\AiUsageService;
use App\Settings\OnboardingSettings;
use BackedEnum;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\Width;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;

class Dashboard extends BaseDashboard
{
    use GeneratesOrganizationBlueprints;

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

    public function dismissOnboarding(): void
    {
        /** @var OnboardingSettings $settings */
        $settings = resolve(OnboardingSettings::class);

        $settings->markDismissed();

        Notification::make()
            ->success()
            ->title('Setup Wizard Dismissed')
            ->body("You won't see this wizard again. You can configure your organization from the settings menu.")
            ->send();
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
        $settings = resolve(OnboardingSettings::class);

        return $settings->isAccessible();
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOnboardingFormData(): array
    {
        return [
            'builder' => [
                'preset' => 'blank',
                'description' => null,
            ],
            'blueprint' => OrganizationBlueprint::toState(),
            'personnel' => [
                'name' => null,
                'email' => null,
                'assign_unit' => null,
                'assign_position' => null,
                'assign_rank' => null,
                'assign_status' => null,
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
            Step::make('Describe')
                ->label('Describe Your Organization')
                ->description('Build your structure in seconds')
                ->icon(Heroicon::OutlinedSparkles)
                ->completedIcon(Heroicon::CheckCircle)
                ->schema([
                    Actions::make([
                        Action::make('dismissOnboarding')
                            ->label("Don't show this setup wizard again")
                            ->icon(Heroicon::OutlinedXCircle)
                            ->color('gray')
                            ->link()
                            ->requiresConfirmation()
                            ->modalHeading('Dismiss Setup Wizard')
                            ->modalDescription("You won't see this wizard again. You can always configure your organization from the settings menu.")
                            ->modalSubmitActionLabel("Yes, Don't Show Again")
                            ->action(function (): void {
                                $this->dismissOnboarding();
                            })
                            ->cancelParentActions(),
                    ])->key('onboardingDismiss')->alignEnd(),
                    Section::make('Describe Your Organization')
                        ->description('Tell us about your organization and we will draft its ranks, groups, units, positions, awards and more, or start from a preset. Prefer to build it yourself? Skip ahead and add everything in the next step.')
                        ->schema([
                            Select::make('builder.preset')
                                ->label('Start From')
                                ->options(fn (): array => ['blank' => 'Start Blank', ...OrganizationBlueprintData::presets()])
                                ->selectablePlaceholder(false)
                                ->live()
                                ->helperText('Choose a preset to start from a ready-made structure, or start blank and build it yourself.'),
                            Textarea::make('builder.description')
                                ->label('Description')
                                ->rows(4)
                                ->maxLength(2000)
                                ->placeholder('e.g., A WWII 101st Airborne milsim unit with three rifle platoons, US Army enlisted and officer ranks, and awards from the Bronze Star to the Medal of Honor.')
                                ->helperText('Describe your organization in a few sentences.')
                                ->visible(fn (): bool => AiUsageService::isAvailable()),
                            Actions::make([
                                Action::make('generateBlueprint')
                                    ->label('Generate Structure')
                                    ->icon(Heroicon::OutlinedSparkles)
                                    ->visible(fn (): bool => AiUsageService::isAvailable())
                                    ->action(function (Get $schemaGet, Set $schemaSet): void {
                                        $this->generateOnboardingBlueprint($schemaGet, $schemaSet);
                                    }),
                                Action::make('loadPreset')
                                    ->label('Use Preset')
                                    ->icon(Heroicon::OutlinedDocumentDuplicate)
                                    ->color('gray')
                                    ->action(function (Get $schemaGet, Set $schemaSet): void {
                                        $preset = (string) $schemaGet('builder.preset');

                                        if (! array_key_exists($preset, OrganizationBlueprintData::presets())) {
                                            Notification::make()
                                                ->warning()
                                                ->title('Preset Required')
                                                ->body('Choose a preset to start from first.')
                                                ->send();

                                            return;
                                        }

                                        $schemaSet('blueprint', OrganizationBlueprint::toState(OrganizationBlueprintData::fromPreset($preset)));
                                        $this->resetPersonnelAssignments($schemaSet);

                                        Notification::make()
                                            ->success()
                                            ->title('Preset Loaded')
                                            ->body('Review and edit it in the next step.')
                                            ->send();
                                    }),
                            ])->key('onboardingBuilderActions'),
                        ]),
                ]),
            Step::make('Structure')
                ->label('Build Your Structure')
                ->description('Ranks, units, positions and more')
                ->icon(Heroicon::OutlinedRectangleGroup)
                ->completedIcon(Heroicon::CheckCircle)
                ->schema([
                    Text::make('structure_info')
                        ->content(fn (Get $get): string => OrganizationBlueprint::isFilled($get('blueprint'))
                            ? 'Review and edit your structure. Everything below is created when you complete setup, and anything that already exists is reused.'
                            : 'Add the ranks, groups and units, positions and statuses your organization uses. You can add the rest any time from the Organization Builder in settings.'),
                    OrganizationBlueprint::make('blueprint'),
                ]),
            Step::make('Personnel')
                ->label('Add Your First Member')
                ->description('Create a personnel record')
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
                                ->helperText('Choose from your structure. Leave empty to use the first unit in it.')
                                ->options(fn (Get $get): array => $this->personnelOptions($get, 'units', Unit::class))
                                ->placeholder('Select a unit or leave empty')
                                ->searchable()
                                ->preload(),
                            Select::make('personnel.assign_position')
                                ->label('Assign Position')
                                ->helperText('Choose from your structure. Leave empty to use the first position in it.')
                                ->options(fn (Get $get): array => $this->personnelOptions($get, 'positions', Position::class))
                                ->placeholder('Select a position or leave empty')
                                ->searchable()
                                ->preload(),
                            Select::make('personnel.assign_rank')
                                ->label('Assign Rank')
                                ->helperText('Choose from your structure. Leave empty to use the first rank in it.')
                                ->options(fn (Get $get): array => $this->personnelOptions($get, 'ranks', Rank::class))
                                ->placeholder('Select a rank or leave empty')
                                ->searchable()
                                ->preload(),
                            Select::make('personnel.assign_status')
                                ->label('Assign Status')
                                ->helperText('Choose from your structure. Leave empty to use the first status in it.')
                                ->options(fn (Get $get): array => $this->personnelOptions($get, 'statuses', Status::class))
                                ->placeholder('Select a status or leave empty')
                                ->searchable()
                                ->preload(),
                        ]),
                ]),
            Step::make('Finish')
                ->description('Helpful resources')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->completedIcon(Heroicon::CheckCircle)
                ->schema([
                    Section::make("You're all set!")
                        ->description('Click "Complete Setup" to create your organization and start using PERSCOM.')
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
        $this->applyBlueprintIfNeeded($data);
        $this->createPersonnelIfNeeded($data);

        /** @var OnboardingSettings $settings */
        $settings = resolve(OnboardingSettings::class);

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

    protected function generateOnboardingBlueprint(Get $get, Set $set): void
    {
        $description = (string) $get('builder.description');

        if (blank($description)) {
            Notification::make()
                ->warning()
                ->title('Description Required')
                ->body('Describe your organization before generating a structure.')
                ->send();

            return;
        }

        $preset = $get('builder.preset');

        $blueprint = $this->generateBlueprint(fn (): OrganizationBlueprintData => OrganizationBlueprintService::generate(
            $description,
            filled($preset) && $preset !== 'blank' ? $preset : null,
        ));

        if (! $blueprint instanceof OrganizationBlueprintData) {
            return;
        }

        $set('blueprint', OrganizationBlueprint::toState($blueprint));
        $this->resetPersonnelAssignments($set);

        Notification::make()
            ->success()
            ->title('Structure Generated')
            ->body('Review and edit it in the next step.')
            ->send();
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @throws Halt
     */
    protected function applyBlueprintIfNeeded(array $data): void
    {
        $state = data_get($data, 'blueprint');

        if (! OrganizationBlueprint::isFilled($state)) {
            return;
        }

        try {
            ApplyOrganizationBlueprint::handle(OrganizationBlueprint::fromState($state));
        } catch (ValidationException $validationException) {
            Notification::make()
                ->danger()
                ->title('Unable to Create Your Structure')
                ->body($validationException->getMessage())
                ->send();

            throw new Halt($validationException->getMessage(), $validationException->getCode(), $validationException);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function createPersonnelIfNeeded(array $data): ?User
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

        return User::create([
            'name' => $name,
            'email' => $email,
            'approved' => true,
            'unit_id' => $this->resolvePersonnelAssignment(data_get($data, 'personnel.assign_unit'), Unit::class),
            'position_id' => $this->resolvePersonnelAssignment(data_get($data, 'personnel.assign_position'), Position::class),
            'rank_id' => $this->resolvePersonnelAssignment(data_get($data, 'personnel.assign_rank'), Rank::class),
            'status_id' => $this->resolvePersonnelAssignment(data_get($data, 'personnel.assign_status'), Status::class),
        ]);
    }

    /**
     * Personnel can be assigned anything in the structure being built or anything that already exists.
     * Options are keyed by name because nothing in the structure is created until setup completes.
     *
     * @param  class-string<Unit|Position|Rank|Status>  $model
     * @return array<string, string>
     */
    protected function personnelOptions(Get $get, string $section, string $model): array
    {
        $blueprint = $get('blueprint') ?? [];

        $planned = $section === 'units'
            ? collect($blueprint['groups'] ?? [])->flatMap(fn (array $group): array => $group['units'] ?? [])
            : collect($blueprint[$section] ?? []);

        return $planned
            ->pluck('name')
            ->merge($model::query()->pluck('name'))
            ->filter()
            ->unique(fn (string $name): string => mb_strtolower($name))
            ->mapWithKeys(fn (string $name): array => [$name => $name])
            ->all();
    }

    /**
     * Names are matched without case, the same way the structure reuses existing records. Leaving a
     * selection empty assigns the first record of that kind.
     *
     * @param  class-string<Unit|Position|Rank|Status>  $model
     */
    protected function resolvePersonnelAssignment(mixed $value, string $model): ?int
    {
        if (blank($value)) {
            return $model::query()->value('id');
        }

        return $model::query()->whereRaw('LOWER(name) = ?', [mb_strtolower((string) $value)])->value('id')
            ?? $model::query()->value('id');
    }

    /**
     * Assignment selects switch to blueprint options once a structure is loaded, so clear selections
     * that pointed at existing records.
     */
    protected function resetPersonnelAssignments(Set $set): void
    {
        foreach (['assign_unit', 'assign_position', 'assign_rank', 'assign_status'] as $field) {
            $set('personnel.'.$field, null);
        }
    }
}
