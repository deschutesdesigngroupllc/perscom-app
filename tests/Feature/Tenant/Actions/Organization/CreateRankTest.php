<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Actions\Organization;

use App\Actions\Organization\CreateRank;
use App\Models\Category;
use App\Models\Rank;
use Illuminate\Validation\ValidationException;
use Tests\Feature\Tenant\TenantTestCase;

class CreateRankTest extends TenantTestCase
{
    public function test_it_creates_a_rank(): void
    {
        $rank = CreateRank::handle(['name' => 'Sergeant', 'abbreviation' => 'SGT', 'paygrade' => 'E-5']);

        $this->assertTrue($rank->wasRecentlyCreated);
        $this->assertDatabaseHas(Rank::class, ['name' => 'Sergeant', 'abbreviation' => 'SGT', 'paygrade' => 'E-5']);
    }

    public function test_it_returns_the_existing_rank_matched_case_insensitively(): void
    {
        $existing = Rank::factory()->createQuietly(['name' => 'Sergeant']);

        $rank = CreateRank::handle(['name' => 'sergeant']);

        $this->assertTrue($rank->is($existing));
        $this->assertSame(1, Rank::query()->where('name', 'Sergeant')->count());
    }

    public function test_it_attaches_a_category_by_name_only_once(): void
    {
        CreateRank::handle(['name' => 'Sergeant', 'category' => 'Enlisted']);
        $rank = CreateRank::handle(['name' => 'Sergeant', 'category' => 'enlisted']);

        $this->assertSame(1, $rank->categories()->count());
        $this->assertSame(1, Category::query()->where('resource', Rank::class)->count());
        $this->assertSame('Enlisted', $rank->categories()->value('name'));
    }

    public function test_it_requires_a_name(): void
    {
        $this->expectException(ValidationException::class);

        CreateRank::handle(['abbreviation' => 'SGT']);
    }
}
