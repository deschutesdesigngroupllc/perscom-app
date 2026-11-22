<?php

declare(strict_types=1);

namespace Tests\Feature\Central\Models\Enums;

use App\Data\Organization\OrganizationBlueprintData;
use App\Models\Enums\OrganizationType;
use Tests\Feature\Central\CentralTestCase;

class OrganizationTypeTest extends CentralTestCase
{
    public function test_every_type_except_other_has_a_blueprint(): void
    {
        $presets = OrganizationBlueprintData::presets();

        collect(OrganizationType::cases())
            ->reject(fn (OrganizationType $type): bool => $type === OrganizationType::OTHER)
            ->each(fn (OrganizationType $type) => $this->assertArrayHasKey($type->blueprintPreset(), $presets));

        $this->assertNull(OrganizationType::OTHER->blueprintPreset());
    }
}
