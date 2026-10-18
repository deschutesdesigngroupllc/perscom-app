<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Actions\Organization;

use App\Actions\Organization\CreatePosition;
use App\Models\Position;
use Illuminate\Validation\ValidationException;
use Tests\Feature\Tenant\TenantTestCase;

class CreatePositionTest extends TenantTestCase
{
    public function test_it_creates_a_position(): void
    {
        $position = CreatePosition::handle(['name' => 'Platoon Leader', 'description' => 'Leads the platoon.']);

        $this->assertTrue($position->wasRecentlyCreated);
        $this->assertDatabaseHas(Position::class, ['name' => 'Platoon Leader', 'description' => 'Leads the platoon.']);
    }

    public function test_it_returns_the_existing_position_matched_case_insensitively(): void
    {
        $existing = Position::factory()->createQuietly(['name' => 'Platoon Leader']);

        $position = CreatePosition::handle(['name' => 'platoon leader']);

        $this->assertTrue($position->is($existing));
        $this->assertSame(1, Position::query()->where('name', 'Platoon Leader')->count());
    }

    public function test_it_requires_a_name(): void
    {
        $this->expectException(ValidationException::class);

        CreatePosition::handle([]);
    }
}
