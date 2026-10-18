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

    public function test_it_nests_units_under_their_parents_across_groups_and_in_any_order(): void
    {
        ApplyOrganizationBlueprint::handle(new OrganizationBlueprintData(groups: [
            new GroupData(name: 'Operations', units: [
                new UnitData(name: '1st Platoon', parent: 'Alpha Company'),
                new UnitData(name: 'Alpha Company', parent: 'Battalion Headquarters'),
            ]),
            new GroupData(name: 'Command', units: [
                new UnitData(name: 'Battalion Headquarters'),
            ]),
        ]));

        $this->assertSame('Alpha Company', $this->unit('1st Platoon')->parent?->name);
        $this->assertSame('Battalion Headquarters', $this->unit('Alpha Company')->parent?->name);
        $this->assertNull($this->unit('Battalion Headquarters')->parent_id);
    }

    public function test_a_parent_can_be_an_existing_unit(): void
    {
        $existing = Unit::factory()->createQuietly(['name' => 'Battalion Headquarters']);

        ApplyOrganizationBlueprint::handle(new OrganizationBlueprintData(groups: [
            new GroupData(name: 'Operations', units: [new UnitData(name: 'Alpha Company', parent: 'battalion headquarters')]),
        ]));

        $this->assertSame($existing->getKey(), $this->unit('Alpha Company')->parent_id);
    }

    public function test_missing_self_and_circular_parents_are_skipped(): void
    {
        ApplyOrganizationBlueprint::handle(new OrganizationBlueprintData(groups: [
            new GroupData(name: 'Operations', units: [
                new UnitData(name: 'Alpha Company', parent: 'Bravo Company'),
                new UnitData(name: 'Bravo Company', parent: 'Alpha Company'),
                new UnitData(name: 'Charlie Company', parent: 'Charlie Company'),
                new UnitData(name: 'Delta Company', parent: 'Nonexistent Battalion'),
            ]),
        ]));

        $this->assertSame('Bravo Company', $this->unit('Alpha Company')->parent?->name);
        $this->assertNull($this->unit('Bravo Company')->parent_id);
        $this->assertNull($this->unit('Charlie Company')->parent_id);
        $this->assertNull($this->unit('Delta Company')->parent_id);
    }

    public function test_presets_include_their_unit_hierarchy(): void
    {
        ApplyOrganizationBlueprint::handle(OrganizationBlueprintData::fromPreset('fire-service'));

        $this->assertSame('Battalion 1', $this->unit('Station 1 - Engine 1, Truck 1, Medic 1')->parent?->name);
        $this->assertSame('District 1 Headquarters', $this->unit('Battalion 1')->parent?->name);
        $this->assertNull($this->unit('District 1 Headquarters')->parent_id);
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

    protected function unit(string $name): Unit
    {
        return Unit::query()->withoutGlobalScopes()->with('parent')->where('name', $name)->firstOrFail();
    }
}
