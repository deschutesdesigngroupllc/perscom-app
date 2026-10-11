<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Actions\Organization;

use App\Actions\Organization\ApplyOrganizationBlueprint;
use App\Data\Organization\GroupData;
use App\Data\Organization\OrganizationBlueprintData;
use App\Data\Organization\SlotData;
use App\Data\Organization\UnitData;
use App\Models\Award;
use App\Models\Form;
use App\Models\Group;
use App\Models\Position;
use App\Models\Rank;
use App\Models\Slot;
use App\Models\Unit;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\Tenant\TenantTestCase;

class ApplyOrganizationBlueprintTest extends TenantTestCase
{
    /**
     * @return array<string, array{string}>
     */
    public static function presets(): array
    {
        return [
            'military' => ['military'],
            'fire service' => ['fire-service'],
            'law enforcement' => ['law-enforcement'],
        ];
    }

    #[DataProvider('presets')]
    public function test_it_applies_a_preset_blueprint(string $preset): void
    {
        $blueprint = OrganizationBlueprintData::fromPreset($preset);

        $summary = ApplyOrganizationBlueprint::handle($blueprint);

        $units = $this->units($blueprint);
        $slots = $units->flatMap(fn (UnitData $unit): array => $unit->slots)
            ->unique(fn (SlotData $slot): string => implode('|', [strtolower($slot->name), $slot->position, $slot->specialty]));

        $this->assertSame(count($blueprint->positions), $summary['positions']['created']);
        $this->assertSame(count($blueprint->ranks), $summary['ranks']['created']);
        $this->assertSame(count($blueprint->groups), $summary['groups']['created']);
        $this->assertSame($units->unique('name')->count(), $summary['units']['created']);
        $this->assertSame($slots->count(), $summary['slots']['created']);
        $this->assertSame(count($blueprint->awards), $summary['awards']['created']);
        $this->assertSame(count($blueprint->forms), $summary['forms']['created']);

        $this->assertSame(count($blueprint->positions), Position::query()->count());
        $this->assertSame(count($blueprint->ranks), Rank::query()->count());
        $this->assertSame(count($blueprint->groups), Group::query()->count());
        $this->assertSame($units->unique('name')->count(), Unit::query()->withoutGlobalScopes()->count());
        $this->assertSame($slots->count(), Slot::query()->withoutGlobalScopes()->count());
        $this->assertSame(count($blueprint->awards), Award::query()->count());
        $this->assertSame(count($blueprint->forms), Form::query()->count());
    }

    public function test_applying_a_blueprint_twice_reuses_existing_records(): void
    {
        $blueprint = OrganizationBlueprintData::fromPreset('military');

        ApplyOrganizationBlueprint::handle($blueprint);
        $summary = ApplyOrganizationBlueprint::handle($blueprint);

        collect($summary)->each(fn (array $counts, string $section) => $this->assertSame(0, $counts['created'], sprintf('Expected no new %s.', $section)));

        $this->assertSame(count($blueprint->ranks), $summary['ranks']['existing']);
        $this->assertSame(count($blueprint->ranks), Rank::query()->count());
    }

    public function test_it_links_units_to_groups_and_slots_to_positions(): void
    {
        $blueprint = OrganizationBlueprintData::from([
            'positions' => [['name' => 'Squad Leader']],
            'specialties' => [['name' => 'Infantry', 'abbreviation' => '11B']],
            'groups' => [[
                'name' => 'Operations',
                'units' => [[
                    'name' => 'First Squad',
                    'slots' => [['name' => 'Squad Leader', 'position' => 'squad leader', 'specialty' => 'Infantry']],
                ]],
            ]],
        ]);

        ApplyOrganizationBlueprint::handle($blueprint);

        $unit = Unit::query()->where('name', 'First Squad')->firstOrFail();
        $slot = Slot::query()->withoutGlobalScopes()->findOrFail($unit->slots()->value('slots.id'));

        $this->assertSame('Operations', $unit->groups()->value('name'));
        $this->assertSame('Squad Leader', $slot->position?->name);
        $this->assertSame('Infantry', $slot->specialty?->name);
    }

    public function test_it_rolls_back_when_an_entity_is_invalid(): void
    {
        $blueprint = OrganizationBlueprintData::from([
            'ranks' => [['name' => 'Private']],
            'credentials' => [['name' => 'First Aid', 'type' => 'diploma']],
        ]);

        try {
            ApplyOrganizationBlueprint::handle($blueprint);
            $this->fail('Expected a validation exception.');
        } catch (ValidationException) {
            $this->assertSame(0, Rank::query()->count());
        }
    }

    /**
     * @return Collection<int, UnitData>
     */
    protected function units(OrganizationBlueprintData $blueprint): Collection
    {
        return collect($blueprint->groups)->flatMap(fn (GroupData $group): array => $group->units);
    }
}
