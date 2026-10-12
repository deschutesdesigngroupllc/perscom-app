<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Actions\Organization;

use App\Actions\Organization\ApplyOrganizationBlueprint;
use App\Actions\Organization\BuildOrganizationBlueprint;
use App\Data\Organization\GroupData;
use App\Data\Organization\OrganizationBlueprintData;
use App\Data\Organization\UnitData;
use Tests\Feature\Tenant\TenantTestCase;

class BuildOrganizationBlueprintTest extends TenantTestCase
{
    public function test_it_describes_the_existing_organization(): void
    {
        $preset = OrganizationBlueprintData::fromPreset('military');

        ApplyOrganizationBlueprint::handle($preset);

        $blueprint = BuildOrganizationBlueprint::handle();

        $this->assertSame(collect($preset->ranks)->pluck('name')->all(), collect($blueprint->ranks)->pluck('name')->all());
        $this->assertSame(collect($preset->groups)->pluck('name')->all(), collect($blueprint->groups)->pluck('name')->all());
        $this->assertCount(count($preset->awards), $blueprint->awards);
        $this->assertCount(count($preset->qualifications), $blueprint->qualifications);
        $this->assertCount(count($preset->forms), $blueprint->forms);
        $this->assertSame($preset->ranks[0]->category, $blueprint->ranks[0]->category);
    }

    public function test_an_exported_blueprint_round_trips_without_creating_duplicates(): void
    {
        ApplyOrganizationBlueprint::handle(OrganizationBlueprintData::fromPreset('fire-service'));

        $exported = OrganizationBlueprintData::fromJson(BuildOrganizationBlueprint::handle()->toJson());

        $summary = ApplyOrganizationBlueprint::handle($exported);

        collect($summary)->each(fn (array $counts, string $section) => $this->assertSame(0, $counts['created'], sprintf('Section [%s] created new records.', $section)));
    }

    public function test_exported_units_include_their_parent(): void
    {
        ApplyOrganizationBlueprint::handle(OrganizationBlueprintData::fromPreset('law-enforcement'));

        $parents = collect(BuildOrganizationBlueprint::handle()->groups)
            ->flatMap(fn (GroupData $group): array => $group->units)
            ->mapWithKeys(fn (UnitData $unit): array => [$unit->name => $unit->parent]);

        $this->assertSame('Office of the Chief', $parents['Patrol Division - A Squad']);
        $this->assertNull($parents['Office of the Chief']);
    }
}
