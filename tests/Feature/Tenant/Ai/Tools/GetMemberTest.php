<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Ai\Tools;

use App\Ai\Tools\GetMember;
use App\Models\Award;
use App\Models\AwardRecord;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Laravel\Ai\Tools\Request;
use Tests\Feature\Tenant\TenantTestCase;

class GetMemberTest extends TenantTestCase
{
    public function test_it_returns_the_member_profile_with_awards(): void
    {
        $actor = User::factory()->createQuietly();
        $actor->assignRole(Utils::getSuperAdminName());
        $member = User::factory()->createQuietly();
        $award = Award::factory()->createQuietly(['name' => 'Bronze Star']);
        AwardRecord::factory()->for($member)->createQuietly(['award_id' => $award->getKey()]);

        $result = json_decode((string) new GetMember($actor)->handle(new Request(['id' => $member->getKey()])), true);

        $this->assertSame($member->getKey(), $result['id']);
        $this->assertSame($member->rank->name, $result['rank']);
        $this->assertSame('Bronze Star', $result['awards'][0]['award']);
    }

    public function test_it_refuses_actors_who_cannot_view_the_member(): void
    {
        $actor = User::factory()->withoutRoles()->createQuietly();
        $member = User::factory()->createQuietly();

        $this->assertSame('You are not allowed to view this member.', (string) new GetMember($actor)->handle(new Request(['id' => $member->getKey()])));
    }

    public function test_it_reports_unknown_members(): void
    {
        $actor = User::factory()->createQuietly();

        $this->assertSame('No member exists with that id.', (string) new GetMember($actor)->handle(new Request(['id' => 999999])));
    }
}
