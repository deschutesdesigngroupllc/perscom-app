<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Actions\Organization;

use App\Actions\Organization\CreateSlot;
use App\Models\Position;
use App\Models\Slot;
use App\Models\Specialty;
use App\Models\Unit;
use Illuminate\Validation\ValidationException;
use Tests\Feature\Tenant\TenantTestCase;

class CreateSlotTest extends TenantTestCase
{
    public function test_it_creates_a_slot_and_attaches_it_to_the_unit(): void
    {
        $unit = Unit::factory()->createQuietly();
        $position = Position::factory()->createQuietly();
        $specialty = Specialty::factory()->createQuietly();

        $slot = CreateSlot::handle([
            'name' => 'Team Leader',
            'position_id' => $position->getKey(),
            'specialty_id' => $specialty->getKey(),
        ], $unit);

        $this->assertTrue($slot->wasRecentlyCreated);
        $this->assertSame($position->getKey(), $slot->position_id);
        $this->assertSame($specialty->getKey(), $slot->specialty_id);
        $this->assertTrue($unit->slots()->whereKey($slot->getKey())->exists());
    }

    public function test_it_reuses_a_slot_with_the_same_name_position_and_specialty(): void
    {
        $first = Unit::factory()->createQuietly();
        $second = Unit::factory()->createQuietly();
        $position = Position::factory()->createQuietly();

        $slot = CreateSlot::handle(['name' => 'Team Leader', 'position_id' => $position->getKey()], $first);
        $again = CreateSlot::handle(['name' => 'team leader', 'position_id' => $position->getKey()], $second);
        CreateSlot::handle(['name' => 'Team Leader', 'position_id' => $position->getKey()], $second);

        $this->assertTrue($again->is($slot));
        $this->assertFalse($again->wasRecentlyCreated);
        $this->assertSame(1, $second->slots()->count());
        $this->assertSame(1, Slot::query()->withoutGlobalScopes()->where('name', 'Team Leader')->count());
    }

    public function test_it_creates_a_separate_slot_for_a_different_position(): void
    {
        $slot = CreateSlot::handle(['name' => 'Team Leader', 'position_id' => Position::factory()->createQuietly()->getKey()]);
        $other = CreateSlot::handle(['name' => 'Team Leader', 'position_id' => Position::factory()->createQuietly()->getKey()]);

        $this->assertFalse($other->is($slot));
    }

    public function test_it_rejects_an_unknown_position(): void
    {
        $this->expectException(ValidationException::class);

        CreateSlot::handle(['name' => 'Team Leader', 'position_id' => 999999]);
    }
}
