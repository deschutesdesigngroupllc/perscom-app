<?php

declare(strict_types=1);

namespace App\Forms\Components;

use App\Data\Organization\OrganizationBlueprintData;
use App\Models\Unit;
use App\Services\Ai\OrganizationBlueprintService;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;
use Illuminate\Validation\ValidationException;

class OrganizationBlueprint
{
    /**
     * An editable preview of an organization blueprint, stored under the given state path.
     */
    public static function make(string $statePath = 'blueprint'): Group
    {
        return Group::make()
            ->statePath($statePath)
            ->columnSpanFull()
            ->schema([
                Hidden::make('name'),
                Hidden::make('description'),
                Hidden::make('credentials'),
                Hidden::make('forms'),
                Tabs::make('Blueprint')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Ranks')
                            ->icon(Heroicon::OutlinedChevronDoubleUp)
                            ->badge(fn (Get $get): int => count($get('ranks') ?? []))
                            ->schema([
                                self::repeater('ranks', 'Add Rank')
                                    ->helperText('Listed from most senior to most junior.')
                                    ->columns(4)
                                    ->schema([
                                        self::name('The name of the rank.'),
                                        TextInput::make('abbreviation')
                                            ->maxLength(255)
                                            ->helperText('The short form of the rank.'),
                                        TextInput::make('paygrade')
                                            ->maxLength(255)
                                            ->helperText('The paygrade of the rank.'),
                                        TextInput::make('category')
                                            ->maxLength(255)
                                            ->helperText('The category the rank belongs to.'),
                                        Hidden::make('description'),
                                    ]),
                            ]),
                        Tab::make('Groups & Units')
                            ->icon(Heroicon::OutlinedRectangleGroup)
                            ->badge(fn (Get $get): int => count($get('groups') ?? []))
                            ->schema([
                                self::repeater('groups', 'Add Group')
                                    ->schema([
                                        self::name('The name of the group.'),
                                        TextInput::make('description')
                                            ->maxLength(65535)
                                            ->helperText('A short description of the group.'),
                                        Hidden::make('icon'),
                                        self::repeater('units', 'Add Unit')
                                            ->label('Units')
                                            ->hiddenLabel(false)
                                            ->columns(3)
                                            ->schema([
                                                self::name('The name of the unit.')
                                                    ->live(onBlur: true),
                                                Select::make('parent')
                                                    ->label('Parent Unit')
                                                    ->placeholder('None (top level)')
                                                    ->searchable()
                                                    ->options(fn (Get $get): array => self::parentUnitOptions($get('../../../../groups'), $get('name')))
                                                    ->helperText('The unit this unit sits under, from any group.'),
                                                TextInput::make('description')
                                                    ->maxLength(65535)
                                                    ->helperText('A short description of the unit.'),
                                                Hidden::make('slots'),
                                            ]),
                                    ]),
                            ]),
                        Tab::make('Positions')
                            ->icon(Heroicon::OutlinedIdentification)
                            ->badge(fn (Get $get): int => count($get('positions') ?? []))
                            ->schema([
                                self::repeater('positions', 'Add Position')
                                    ->columns(2)
                                    ->schema([
                                        self::name('The name of the position.'),
                                        TextInput::make('description')
                                            ->maxLength(65535)
                                            ->helperText('A short description of the position.'),
                                    ]),
                            ]),
                        Tab::make('Specialties')
                            ->icon(Heroicon::OutlinedBriefcase)
                            ->badge(fn (Get $get): int => count($get('specialties') ?? []))
                            ->schema([
                                self::repeater('specialties', 'Add Specialty')
                                    ->columns(3)
                                    ->schema([
                                        self::name('The name of the specialty.'),
                                        TextInput::make('abbreviation')
                                            ->maxLength(255)
                                            ->helperText('The short form of the specialty.'),
                                        TextInput::make('description')
                                            ->maxLength(65535)
                                            ->helperText('A short description of the specialty.'),
                                    ]),
                            ]),
                        Tab::make('Statuses')
                            ->icon(Heroicon::OutlinedScale)
                            ->badge(fn (Get $get): int => count($get('statuses') ?? []))
                            ->schema([
                                self::repeater('statuses', 'Add Status')
                                    ->columns(2)
                                    ->schema([
                                        self::name('The name of the status.'),
                                        ColorPicker::make('color')
                                            ->helperText('The color used to display the status.'),
                                    ]),
                            ]),
                        Tab::make('Awards')
                            ->icon(Heroicon::OutlinedTrophy)
                            ->badge(fn (Get $get): int => count($get('awards') ?? []))
                            ->schema([
                                self::repeater('awards', 'Add Award')
                                    ->columns(3)
                                    ->schema([
                                        self::name('The name of the award.'),
                                        TextInput::make('category')
                                            ->maxLength(255)
                                            ->helperText('The category the award belongs to.'),
                                        TextInput::make('description')
                                            ->maxLength(65535)
                                            ->helperText('A short description of the award.'),
                                    ]),
                            ]),
                        Tab::make('Qualifications')
                            ->icon(Heroicon::OutlinedAcademicCap)
                            ->badge(fn (Get $get): int => count($get('qualifications') ?? []))
                            ->schema([
                                self::repeater('qualifications', 'Add Qualification')
                                    ->columns(3)
                                    ->schema([
                                        self::name('The name of the qualification.'),
                                        TextInput::make('category')
                                            ->maxLength(255)
                                            ->helperText('The category the qualification belongs to.'),
                                        TextInput::make('description')
                                            ->maxLength(65535)
                                            ->helperText('A short description of the qualification.'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    /**
     * The form state for a blueprint.
     *
     * @return array<string, mixed>
     */
    public static function toState(?OrganizationBlueprintData $blueprint = null): array
    {
        return ($blueprint ?? new OrganizationBlueprintData)->toArray();
    }

    /**
     * Convert form state back into a validated blueprint.
     *
     * @param  array<string, mixed>|null  $state
     *
     * @throws ValidationException
     */
    public static function fromState(?array $state): OrganizationBlueprintData
    {
        return OrganizationBlueprintService::toBlueprint($state ?? []);
    }

    /**
     * Whether the form state describes anything to create.
     *
     * @param  array<string, mixed>|null  $state
     */
    public static function isFilled(?array $state): bool
    {
        return collect([...OrganizationBlueprintService::NAMED_SECTIONS, 'groups'])
            ->contains(fn (string $section): bool => filled($state[$section] ?? null));
    }

    protected static function repeater(string $name, string $addActionLabel): Repeater
    {
        return Repeater::make($name)
            ->hiddenLabel()
            ->defaultItems(0)
            ->reorderable()
            ->collapsible()
            ->cloneable()
            ->addActionLabel($addActionLabel)
            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null);
    }

    /**
     * Any unit in the blueprint, from any group, or one that already exists can be a parent. Options
     * are keyed by name because nothing in the blueprint exists until it is applied.
     *
     * @param  array<string, mixed>|null  $groups
     * @return array<string, string>
     */
    protected static function parentUnitOptions(?array $groups, ?string $currentName): array
    {
        return collect($groups ?? [])
            ->flatMap(fn (mixed $group): array => is_array($group) ? (array) ($group['units'] ?? []) : [])
            ->pluck('name')
            ->merge(Unit::query()->withoutGlobalScopes()->orderBy('name')->pluck('name'))
            ->filter(fn (mixed $name): bool => filled($name) && mb_strtolower((string) $name) !== mb_strtolower((string) $currentName))
            ->unique(fn (string $name): string => mb_strtolower($name))
            ->mapWithKeys(fn (string $name): array => [$name => $name])
            ->all();
    }

    protected static function name(string $helperText): TextInput
    {
        return TextInput::make('name')
            ->required()
            ->maxLength(255)
            ->helperText($helperText);
    }
}
