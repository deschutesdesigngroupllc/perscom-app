<?php

declare(strict_types=1);

namespace App\Services\Ai;

use App\Actions\Organization\BuildOrganizationBlueprint;
use App\Ai\Agents\OrganizationBlueprintAgent;
use App\Data\Organization\GroupData;
use App\Data\Organization\OrganizationBlueprintData;
use App\Exceptions\AiUsageLimitExceededException;
use App\Services\AiUsageService;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Ai\Responses\StructuredAgentResponse;

class OrganizationBlueprintService
{
    /**
     * The flat blueprint sections whose entries are matched by name.
     */
    public const array NAMED_SECTIONS = [
        'positions',
        'specialties',
        'ranks',
        'statuses',
        'awards',
        'qualifications',
        'credentials',
        'forms',
    ];

    /**
     * Design a complete organization from a plain-language description, optionally starting from a preset.
     *
     * @throws AiUsageLimitExceededException
     * @throws ValidationException
     */
    public static function generate(string $description, ?string $preset = null): OrganizationBlueprintData
    {
        AiUsageService::ensureWithinLimit();

        $prompt = "Design the complete organizational structure for the following organization:\n\n".trim($description);

        if (filled($preset)) {
            $prompt .= sprintf(
                "\n\nUse this %s blueprint as a starting point and adapt it to the description above:\n\n%s",
                OrganizationBlueprintData::presets()[$preset] ?? $preset,
                OrganizationBlueprintData::fromPreset($preset)->toJson(),
            );
        }

        return static::prompt($prompt);
    }

    /**
     * Apply a plain-language change to a blueprint and return the complete updated blueprint.
     *
     * @throws AiUsageLimitExceededException
     * @throws ValidationException
     */
    public static function refine(OrganizationBlueprintData $current, string $instruction): OrganizationBlueprintData
    {
        AiUsageService::ensureWithinLimit();

        return static::prompt(sprintf(
            "Here is the current organization blueprint as JSON:\n\n%s\n\nApply the following change and return the complete updated blueprint, keeping everything that was not asked to change:\n\n%s",
            $current->toJson(),
            trim($instruction),
        ));
    }

    /**
     * Propose additions to the organization's existing structure. Only new entities are returned.
     *
     * @throws AiUsageLimitExceededException
     * @throws ValidationException
     */
    public static function extend(string $instruction): OrganizationBlueprintData
    {
        AiUsageService::ensureWithinLimit();

        $existing = BuildOrganizationBlueprint::handle();

        $blueprint = static::prompt(sprintf(
            "The organization already has the following structure. Never repeat any of these names:\n\n%s\n\nPropose only the new entities needed for this request. Units may be placed in an existing group by using that group's exact name:\n\n%s",
            static::summarize($existing),
            trim($instruction),
        ));

        return static::withoutExisting($blueprint, $existing);
    }

    /**
     * Convert a structured agent response into a validated blueprint, discarding unnamed entries.
     *
     * @param  array<string, mixed>  $structured
     *
     * @throws ValidationException
     */
    public static function toBlueprint(array $structured): OrganizationBlueprintData
    {
        $named = fn (mixed $items): array => collect(is_array($items) ? $items : [])
            ->filter(fn (mixed $item): bool => is_array($item) && filled($item['name'] ?? null))
            ->values()
            ->all();

        $structured = collect($structured)
            ->only(['name', 'description', ...self::NAMED_SECTIONS, 'groups'])
            ->map(fn (mixed $value, string $key): mixed => in_array($key, [...self::NAMED_SECTIONS, 'groups'], true) ? $named($value) : $value)
            ->all();

        $structured['groups'] = collect($structured['groups'] ?? [])
            ->map(fn (array $group): array => [
                ...$group,
                'units' => collect($named($group['units'] ?? []))
                    ->map(fn (array $unit): array => [...$unit, 'slots' => $named($unit['slots'] ?? [])])
                    ->all(),
            ])
            ->all();

        $structured['forms'] = collect($structured['forms'] ?? [])
            ->map(fn (array $form): array => [...$form, 'fields' => $named($form['fields'] ?? [])])
            ->all();

        return OrganizationBlueprintData::validateAndCreate(static::withoutEmptyArrays($structured));
    }

    /**
     * A compact, name-only description of a blueprint for use in prompts.
     */
    public static function summarize(OrganizationBlueprintData $blueprint): string
    {
        $names = fn (array $items): string => collect($items)->pluck('name')->implode(', ') ?: 'none';

        return collect([
            'Positions: '.$names($blueprint->positions),
            'Specialties: '.$names($blueprint->specialties),
            'Ranks (most senior first): '.$names($blueprint->ranks),
            'Statuses: '.$names($blueprint->statuses),
            'Groups and units: '.(collect($blueprint->groups)
                ->map(fn (GroupData $group): string => sprintf('%s (%s)', $group->name, $names($group->units)))
                ->implode('; ') ?: 'none'),
            'Awards: '.$names($blueprint->awards),
            'Qualifications: '.$names($blueprint->qualifications),
            'Credentials: '.$names($blueprint->credentials),
            'Forms: '.$names($blueprint->forms),
        ])->implode("\n");
    }

    /**
     * Remove anything from the proposed blueprint that already exists in the organization.
     */
    public static function withoutExisting(OrganizationBlueprintData $proposed, OrganizationBlueprintData $existing): OrganizationBlueprintData
    {
        $existingNames = fn (array $items): Collection => collect($items)->map(fn (object $item): string => Str::lower($item->name));
        $isNew = fn (array $existingItems): callable => fn (object $item): bool => ! $existingNames($existingItems)->contains(Str::lower($item->name));

        $sections = collect(self::NAMED_SECTIONS)
            ->mapWithKeys(fn (string $section): array => [
                $section => collect($proposed->{$section})->filter($isNew($existing->{$section}))->values()->all(),
            ])
            ->all();

        $existingUnits = collect($existing->groups)->flatMap(fn (GroupData $group): array => $group->units)->all();
        $existingGroups = $existingNames($existing->groups);

        $groups = collect($proposed->groups)
            ->map(fn (GroupData $group): GroupData => new GroupData(
                name: $group->name,
                description: $group->description,
                icon: $group->icon,
                units: collect($group->units)->filter($isNew($existingUnits))->values()->all(),
            ))
            ->filter(fn (GroupData $group): bool => filled($group->units) || ! $existingGroups->contains(Str::lower($group->name)))
            ->values()
            ->all();

        return new OrganizationBlueprintData(...[
            'name' => $proposed->name,
            'description' => $proposed->description,
            'groups' => $groups,
            ...$sections,
        ]);
    }

    /**
     * Remove empty arrays so the blueprint's defaults apply instead of failing validation.
     *
     * @param  array<array-key, mixed>  $values
     * @return array<array-key, mixed>
     */
    protected static function withoutEmptyArrays(array $values): array
    {
        return collect($values)
            ->map(fn (mixed $value): mixed => is_array($value) ? static::withoutEmptyArrays($value) : $value)
            ->reject(fn (mixed $value): bool => $value === [])
            ->all();
    }

    /**
     * @throws ValidationException
     */
    protected static function prompt(string $prompt): OrganizationBlueprintData
    {
        $response = new OrganizationBlueprintAgent()->prompt($prompt);

        if (! $response instanceof StructuredAgentResponse) {
            throw ValidationException::withMessages([
                'blueprint' => 'The AI did not return a structured organization.',
            ]);
        }

        return static::toBlueprint($response->toArray());
    }
}
