<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\Forms\Components;

use App\Filament\Forms\Components\MemberSelect;
use App\Models\Rank;
use App\Models\Unit;
use App\Models\User;
use Tests\Feature\Tenant\TenantTestCase;

class MemberSelectTest extends TenantTestCase
{
    public function test_labels_include_rank_and_unit(): void
    {
        $rank = Rank::factory()->createQuietly(['abbreviation' => 'SSG']);
        $unit = Unit::factory()->createQuietly(['name' => 'Alpha Company']);
        $member = User::factory()->createQuietly(['name' => 'Jordan Reyes', 'rank_id' => $rank->getKey(), 'unit_id' => $unit->getKey()]);

        $this->assertSame('SSG Jordan Reyes · Alpha Company', MemberSelect::memberLabel($member));
    }

    public function test_labels_fall_back_to_the_name(): void
    {
        $member = User::factory()->createQuietly(['name' => 'Jordan Reyes', 'rank_id' => null, 'unit_id' => null]);

        $this->assertSame('Jordan Reyes', MemberSelect::memberLabel($member));
    }

    public function test_search_labels_include_rank_and_unit(): void
    {
        $rank = Rank::factory()->createQuietly(['abbreviation' => 'SFC']);
        $unit = Unit::factory()->createQuietly(['name' => 'Bravo Company']);
        $member = User::factory()->createQuietly(['name' => 'Quinn Searchable', 'rank_id' => $rank->getKey(), 'unit_id' => $unit->getKey()]);

        $this->assertSame('SFC Quinn Searchable · Bravo Company', MemberSelect::searchMembers('Quinn Searchable')[$member->getKey()]);
    }

    public function test_search_matches_name_or_email(): void
    {
        $byName = User::factory()->createQuietly(['name' => 'Zebulon Quartermain']);
        $byEmail = User::factory()->createQuietly(['email' => 'zebulon.ops@example.test']);

        $results = MemberSelect::searchMembers('zebulon');

        $this->assertArrayHasKey($byName->getKey(), $results);
        $this->assertArrayHasKey($byEmail->getKey(), $results);
    }

    public function test_search_is_capped(): void
    {
        User::factory()->count(MemberSelect::SEARCH_LIMIT + 5)->createQuietly(['name' => 'Capped Member']);

        $this->assertCount(MemberSelect::SEARCH_LIMIT, MemberSelect::searchMembers('Capped'));
    }
}
