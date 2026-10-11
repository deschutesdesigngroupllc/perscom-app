<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Ai\Tools;

use App\Ai\Tools\ListOrganization;
use App\Models\Rank;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Laravel\Ai\Tools\Request;
use Tests\Feature\Tenant\TenantTestCase;

class ListOrganizationTest extends TenantTestCase
{
    public function test_it_lists_the_requested_entities_with_ids(): void
    {
        $actor = User::factory()->createQuietly();
        $actor->assignRole(Utils::getSuperAdminName());
        $rank = Rank::factory()->createQuietly(['name' => 'Sergeant', 'abbreviation' => 'SGT']);

        $result = collect(json_decode((string) new ListOrganization($actor)->handle(new Request(['type' => 'ranks'])), true));

        $this->assertTrue($result->contains(fn (array $item): bool => $item['id'] === $rank->getKey() && $item['name'] === 'Sergeant' && $item['abbreviation'] === 'SGT'));
    }

    public function test_it_rejects_unknown_types(): void
    {
        $actor = User::factory()->createQuietly();

        $this->assertStringStartsWith('Unknown type.', (string) new ListOrganization($actor)->handle(new Request(['type' => 'users'])));
    }

    public function test_it_refuses_actors_who_cannot_view_the_entities(): void
    {
        $actor = User::factory()->withoutRoles()->createQuietly();

        $this->assertSame('You are not allowed to view these.', (string) new ListOrganization($actor)->handle(new Request(['type' => 'awards'])));
    }
}
