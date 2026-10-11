<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Actions\Organization;

use App\Actions\Organization\CreateAward;
use App\Models\Award;
use Illuminate\Validation\ValidationException;
use Tests\Feature\Tenant\TenantTestCase;

class CreateAwardTest extends TenantTestCase
{
    public function test_it_creates_an_award_with_a_category(): void
    {
        $award = CreateAward::handle(['name' => 'Bronze Star', 'description' => 'For valor.', 'category' => 'Decorations']);

        $this->assertTrue($award->wasRecentlyCreated);
        $this->assertDatabaseHas(Award::class, ['name' => 'Bronze Star', 'description' => 'For valor.']);
        $this->assertSame('Decorations', $award->categories()->value('name'));
    }

    public function test_it_returns_the_existing_award_matched_case_insensitively(): void
    {
        $existing = Award::factory()->createQuietly(['name' => 'Bronze Star']);

        $award = CreateAward::handle(['name' => 'bronze star']);

        $this->assertTrue($award->is($existing));
        $this->assertSame(1, Award::query()->where('name', 'Bronze Star')->count());
    }

    public function test_it_requires_a_name(): void
    {
        $this->expectException(ValidationException::class);

        CreateAward::handle(['description' => 'For valor.']);
    }
}
