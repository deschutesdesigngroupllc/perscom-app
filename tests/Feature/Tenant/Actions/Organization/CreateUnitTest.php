<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Actions\Organization;

use App\Actions\Organization\CreateUnit;
use App\Models\Group;
use App\Models\Unit;
use Illuminate\Validation\ValidationException;
use Tests\Feature\Tenant\TenantTestCase;

class CreateUnitTest extends TenantTestCase
{
    public function test_it_creates_a_unit_and_attaches_it_to_the_group(): void
    {
        $group = Group::factory()->createQuietly();

        $unit = CreateUnit::handle(['name' => 'Alpha Company', 'description' => 'First line company.'], $group);

        $this->assertTrue($unit->wasRecentlyCreated);
        $this->assertSame('First line company.', $unit->description);
        $this->assertTrue($unit->groups()->whereKey($group->getKey())->exists());
    }

    public function test_it_reuses_an_existing_unit_and_attaches_the_group_only_once(): void
    {
        $group = Group::factory()->createQuietly();
        $existing = Unit::factory()->createQuietly(['name' => 'Alpha Company']);

        CreateUnit::handle(['name' => 'alpha company'], $group);
        $unit = CreateUnit::handle(['name' => 'Alpha Company'], $group);

        $this->assertTrue($unit->is($existing));
        $this->assertSame(1, Unit::query()->where('name', 'Alpha Company')->count());
        $this->assertSame(1, $unit->groups()->count());
    }

    public function test_it_creates_a_unit_without_a_group(): void
    {
        $unit = CreateUnit::handle(['name' => 'Headquarters']);

        $this->assertSame(0, $unit->groups()->count());
    }

    public function test_it_requires_a_name(): void
    {
        $this->expectException(ValidationException::class);

        CreateUnit::handle(['name' => null]);
    }
}
