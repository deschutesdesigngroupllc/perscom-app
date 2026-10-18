<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Settings\Pages;

use App\Actions\Organization\ApplyOrganizationBlueprint;
use App\Actions\Organization\BuildOrganizationBlueprint;
use App\Data\Organization\OrganizationBlueprintData;
use App\Filament\App\Clusters\Settings;
use App\Filament\Concerns\GeneratesOrganizationBlueprints;
use App\Forms\Components\OrganizationBlueprint;
use App\Services\Ai\OrganizationBlueprintService;
use App\Services\AiUsageService;
use BackedEnum;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

/**
 * @property-read Schema $form
 */
class OrganizationBuilder extends Page
{
    use GeneratesOrganizationBlueprints;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    protected static ?string $cluster = Settings::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|UnitEnum|null $navigationGroup = 'AI & Integrations';

    protected static ?string $navigationLabel = 'Organization Builder';

    protected static ?int $navigationSort = 10;

    protected static ?string $slug = 'dashboard/organization-builder';

    protected static ?string $title = 'Organization Builder';

    protected ?string $subheading = 'Grow your organization by describing what you need, importing a preset, or exporting your current structure.';

    public static function canAccess(): bool
    {
        return parent::canAccess()
            && Auth::user()?->hasRole(Utils::getSuperAdminName())
            && ! App::isDemo();
    }

    public function mount(): void
    {
        $this->form->fill([
            'instruction' => null,
            'preset' => null,
            'blueprint' => OrganizationBlueprint::toState(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Describe What You Need')
                    ->description('Describe the additions you want and we will propose them. Nothing is created until you apply the proposal.')
                    ->visible(fn (): bool => AiUsageService::isAvailable())
                    ->schema([
                        Textarea::make('instruction')
                            ->hiddenLabel()
                            ->rows(3)
                            ->maxLength(2000)
                            ->placeholder('e.g., Add a medical platoon with a corpsman specialty and a Combat Medical Badge qualification.')
                            ->helperText('Describe the units, ranks, awards or other structure you want to add.'),
                        Actions::make([
                            Action::make('propose')
                                ->label('Propose Additions')
                                ->icon(Heroicon::OutlinedSparkles)
                                ->action(fn () => $this->propose()),
                        ]),
                    ]),
                Text::make('ai_unavailable')
                    ->content('AI generation is not available for your organization right now. You can still import a preset or edit the proposal below by hand.')
                    ->visible(fn (): bool => ! AiUsageService::isAvailable()),
                Section::make('Import a Preset')
                    ->description('Load a ready-made structure into the proposal. Anything that already exists is skipped when applied.')
                    ->collapsible()
                    ->schema([
                        Select::make('preset')
                            ->options(fn (): array => OrganizationBlueprintData::presets())
                            ->helperText('The preset to load into the proposal.'),
                        Actions::make([
                            Action::make('importPreset')
                                ->label('Import Preset')
                                ->icon(Heroicon::OutlinedArrowDownTray)
                                ->color('gray')
                                ->action(fn () => $this->importPreset()),
                        ]),
                    ]),
                Section::make('Proposal')
                    ->description('Review and edit the proposed structure, then apply it to create everything at once.')
                    ->schema([
                        OrganizationBlueprint::make('blueprint'),
                        Actions::make([
                            Action::make('apply')
                                ->label('Apply Proposal')
                                ->icon(Heroicon::OutlinedCheckCircle)
                                ->requiresConfirmation()
                                ->modalHeading('Apply Proposal')
                                ->modalDescription('Everything in the proposal will be created. Entities that already exist are skipped.')
                                ->modalSubmitActionLabel('Apply')
                                ->action(fn () => $this->apply()),
                        ]),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                EmbeddedSchema::make('form'),
            ]);
    }

    public function propose(): void
    {
        $instruction = (string) data_get($this->data, 'instruction');

        if (blank($instruction)) {
            Notification::make()
                ->warning()
                ->title('Description Required')
                ->body('Describe what you would like to add first.')
                ->send();

            return;
        }

        $blueprint = $this->generateBlueprint(fn (): OrganizationBlueprintData => OrganizationBlueprintService::extend($instruction));

        if (! $blueprint instanceof OrganizationBlueprintData) {
            return;
        }

        data_set($this->data, 'blueprint', OrganizationBlueprint::toState($blueprint));

        Notification::make()
            ->success()
            ->title('Proposal Ready')
            ->body('Review the proposal below and apply it when you are happy with it.')
            ->send();
    }

    public function importPreset(): void
    {
        $preset = data_get($this->data, 'preset');

        if (blank($preset) || ! array_key_exists($preset, OrganizationBlueprintData::presets())) {
            Notification::make()
                ->warning()
                ->title('Preset Required')
                ->body('Choose a preset to import.')
                ->send();

            return;
        }

        data_set($this->data, 'blueprint', OrganizationBlueprint::toState(OrganizationBlueprintData::fromPreset($preset)));

        Notification::make()
            ->success()
            ->title('Preset Imported')
            ->body('Review the proposal below and apply it when you are happy with it.')
            ->send();
    }

    public function apply(): void
    {
        $state = data_get($this->data, 'blueprint');

        if (! OrganizationBlueprint::isFilled($state)) {
            Notification::make()
                ->warning()
                ->title('Nothing to Apply')
                ->body('The proposal is empty.')
                ->send();

            return;
        }

        try {
            $summary = ApplyOrganizationBlueprint::handle(OrganizationBlueprint::fromState($state));
        } catch (ValidationException $validationException) {
            Notification::make()
                ->danger()
                ->title('Unable to Apply Proposal')
                ->body($validationException->getMessage())
                ->send();

            return;
        }

        $created = collect($summary)
            ->filter(fn (array $counts): bool => $counts['created'] > 0)
            ->map(fn (array $counts, string $section): string => sprintf('%d %s', $counts['created'], Str::headline($section)))
            ->implode(', ');

        data_set($this->data, 'blueprint', OrganizationBlueprint::toState());
        data_set($this->data, 'instruction', null);

        Notification::make()
            ->success()
            ->title('Proposal Applied')
            ->body(filled($created) ? 'Created '.$created.'.' : 'Everything in the proposal already existed.')
            ->send();
    }

    /**
     * @return Action[]
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportBlueprint')
                ->label('Export Blueprint')
                ->icon(Heroicon::OutlinedDocumentArrowDown)
                ->color('gray')
                ->action(fn (): StreamedResponse => response()->streamDownload(
                    function (): void {
                        echo BuildOrganizationBlueprint::handle()->toJson(JSON_PRETTY_PRINT);
                    },
                    'organization-blueprint.json',
                    ['Content-Type' => 'application/json'],
                )),
        ];
    }
}
