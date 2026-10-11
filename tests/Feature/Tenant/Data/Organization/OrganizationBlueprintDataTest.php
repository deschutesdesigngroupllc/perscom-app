<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Data\Organization;

use App\Data\Organization\FormData;
use App\Data\Organization\GroupData;
use App\Data\Organization\OrganizationBlueprintData;
use App\Data\Organization\RankData;
use App\Data\Organization\SlotData;
use App\Data\Organization\UnitData;
use InvalidArgumentException;
use Tests\Feature\Tenant\TenantTestCase;

class OrganizationBlueprintDataTest extends TenantTestCase
{
    public function test_it_lists_the_bundled_presets(): void
    {
        $this->assertSame([
            'fire-service' => 'Fire Service',
            'law-enforcement' => 'Law Enforcement',
            'military' => 'Military',
        ], OrganizationBlueprintData::presets());
    }

    public function test_it_loads_a_preset_into_nested_data_objects(): void
    {
        $blueprint = OrganizationBlueprintData::fromPreset('military');

        $this->assertSame('Military', $blueprint->name);
        $this->assertContainsOnlyInstancesOf(RankData::class, $blueprint->ranks);
        $this->assertContainsOnlyInstancesOf(GroupData::class, $blueprint->groups);
        $this->assertContainsOnlyInstancesOf(UnitData::class, $blueprint->groups[0]->units);
        $this->assertContainsOnlyInstancesOf(SlotData::class, $blueprint->groups[0]->units[0]->slots);
        $this->assertContainsOnlyInstancesOf(FormData::class, $blueprint->forms);
        $this->assertNotEmpty($blueprint->forms[0]->fields);
    }

    public function test_it_round_trips_through_json(): void
    {
        $blueprint = OrganizationBlueprintData::fromPreset('fire-service');

        $json = $blueprint->toJson();
        $restored = OrganizationBlueprintData::fromJson($json);

        $this->assertSame($blueprint->toArray(), $restored->toArray());
        $this->assertArrayHasKey('success_message', json_decode($json, true)['forms'][0]);
        $this->assertArrayHasKey('is_public', json_decode($json, true)['forms'][0]);
    }

    public function test_it_applies_defaults_for_missing_sections(): void
    {
        $blueprint = OrganizationBlueprintData::from(['ranks' => [['name' => 'Private']]]);

        $this->assertNull($blueprint->name);
        $this->assertSame([], $blueprint->groups);
        $this->assertSame('Private', $blueprint->ranks[0]->name);
    }

    public function test_it_throws_for_an_unknown_preset(): void
    {
        $this->expectException(InvalidArgumentException::class);

        OrganizationBlueprintData::fromPreset('../composer');
    }
}
