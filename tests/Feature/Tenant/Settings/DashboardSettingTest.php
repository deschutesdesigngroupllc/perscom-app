<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Settings;

use App\Models\Rank;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\Feature\Tenant\TenantTestCase;

class DashboardSettingTest extends TenantTestCase
{
    public function test_roster_sort_order_sorts_users_correctly()
    {
        $ranks = Rank::factory()->count(5)->state(new Sequence(
            ['name' => 'Rank1', 'order' => 1],
            ['name' => 'Rank2', 'order' => 2],
            ['name' => 'Rank3', 'order' => 3],
            ['name' => 'Rank4', 'order' => 4],
            ['name' => 'Rank5', 'order' => 5],
        ))->createQuietly();

        User::factory()->count(5)->recycle($ranks)->create();

        $ranks = User::orderForRoster()->get()->pluck('rank.order');
        $ranksCopy = $ranks->sort();

        $this->assertSame($ranks->toArray(), $ranksCopy->toArray());
    }
}
