<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Actions\Organization;

use App\Actions\Organization\CreateSpecialty;
use App\Models\Specialty;
use Illuminate\Validation\ValidationException;
use Tests\Feature\Tenant\TenantTestCase;

class CreateSpecialtyTest extends TenantTestCase
{
    public function test_it_creates_a_specialty(): void
    {
        $specialty = CreateSpecialty::handle(['name' => 'Combat Medic', 'abbreviation' => '68W']);

        $this->assertTrue($specialty->wasRecentlyCreated);
        $this->assertDatabaseHas(Specialty::class, ['name' => 'Combat Medic', 'abbreviation' => '68W']);
    }

    public function test_it_returns_the_existing_specialty_matched_case_insensitively(): void
    {
        $existing = Specialty::factory()->createQuietly(['name' => 'Combat Medic']);

        $specialty = CreateSpecialty::handle(['name' => 'COMBAT MEDIC', 'abbreviation' => 'XX']);

        $this->assertTrue($specialty->is($existing));
        $this->assertSame(1, Specialty::query()->where('name', 'Combat Medic')->count());
    }

    public function test_it_requires_a_name(): void
    {
        $this->expectException(ValidationException::class);

        CreateSpecialty::handle(['abbreviation' => '68W']);
    }
}
