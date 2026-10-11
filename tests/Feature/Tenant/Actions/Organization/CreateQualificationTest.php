<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Actions\Organization;

use App\Actions\Organization\CreateQualification;
use App\Models\Qualification;
use Illuminate\Validation\ValidationException;
use Tests\Feature\Tenant\TenantTestCase;

class CreateQualificationTest extends TenantTestCase
{
    public function test_it_creates_a_qualification_with_a_category(): void
    {
        $qualification = CreateQualification::handle(['name' => 'Airborne School', 'category' => 'Schools']);

        $this->assertTrue($qualification->wasRecentlyCreated);
        $this->assertDatabaseHas(Qualification::class, ['name' => 'Airborne School']);
        $this->assertSame('Schools', $qualification->categories()->value('name'));
    }

    public function test_it_returns_the_existing_qualification_matched_case_insensitively(): void
    {
        $existing = Qualification::factory()->createQuietly(['name' => 'Airborne School']);

        $qualification = CreateQualification::handle(['name' => 'AIRBORNE SCHOOL']);

        $this->assertTrue($qualification->is($existing));
        $this->assertSame(1, Qualification::query()->where('name', 'Airborne School')->count());
    }

    public function test_it_requires_a_name(): void
    {
        $this->expectException(ValidationException::class);

        CreateQualification::handle([]);
    }
}
