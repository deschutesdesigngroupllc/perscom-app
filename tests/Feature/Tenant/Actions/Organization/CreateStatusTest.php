<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Actions\Organization;

use App\Actions\Organization\CreateStatus;
use App\Models\Status;
use Illuminate\Validation\ValidationException;
use Tests\Feature\Tenant\TenantTestCase;

class CreateStatusTest extends TenantTestCase
{
    public function test_it_creates_a_status(): void
    {
        $status = CreateStatus::handle(['name' => 'Active Duty', 'color' => '#16a34a']);

        $this->assertTrue($status->wasRecentlyCreated);
        $this->assertDatabaseHas(Status::class, ['name' => 'Active Duty', 'color' => '#16a34a']);
    }

    public function test_it_returns_the_existing_status_matched_case_insensitively(): void
    {
        $existing = Status::factory()->createQuietly(['name' => 'Active Duty']);

        $status = CreateStatus::handle(['name' => 'ACTIVE DUTY', 'color' => '#000000']);

        $this->assertTrue($status->is($existing));
        $this->assertSame(1, Status::query()->where('name', 'Active Duty')->count());
    }

    public function test_it_requires_a_name(): void
    {
        $this->expectException(ValidationException::class);

        CreateStatus::handle(['color' => '#000000']);
    }
}
