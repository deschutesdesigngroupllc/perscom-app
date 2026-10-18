<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Actions\Organization;

use App\Actions\Organization\CreateGroup;
use App\Models\Group;
use Illuminate\Validation\ValidationException;
use Tests\Feature\Tenant\TenantTestCase;

class CreateGroupTest extends TenantTestCase
{
    public function test_it_creates_a_group(): void
    {
        $group = CreateGroup::handle([
            'name' => 'Operations',
            'description' => 'Operational units.',
            'icon' => 'heroicon-o-fire',
        ]);

        $this->assertTrue($group->wasRecentlyCreated);
        $this->assertDatabaseHas(Group::class, [
            'name' => 'Operations',
            'description' => 'Operational units.',
            'icon' => 'heroicon-o-fire',
        ]);
    }

    public function test_it_returns_the_existing_group_matched_case_insensitively(): void
    {
        $existing = Group::factory()->createQuietly(['name' => 'Operations']);

        $group = CreateGroup::handle(['name' => 'OPERATIONS']);

        $this->assertTrue($group->is($existing));
        $this->assertFalse($group->wasRecentlyCreated);
        $this->assertSame(1, Group::query()->where('name', 'Operations')->count());
    }

    public function test_it_requires_a_name(): void
    {
        $this->expectException(ValidationException::class);

        CreateGroup::handle(['name' => '']);
    }
}
