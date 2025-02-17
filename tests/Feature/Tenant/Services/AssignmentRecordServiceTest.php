<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Services;

use App\Models\AssignmentRecord;
use App\Models\Enums\AssignmentRecordType;
use App\Models\Position;
use App\Models\Slot;
use App\Models\Specialty;
use App\Models\Status;
use App\Models\Unit;
use App\Models\User;
use Tests\Feature\Tenant\TenantTestCase;

class AssignmentRecordServiceTest extends TenantTestCase
{
    public function test_create_primary_assignment_record_assigns_user_properties(): void
    {
        $assignment = AssignmentRecord::factory()
            ->state([
                'status_id' => Status::factory(),
                'unit_id' => Unit::factory(),
                'position_id' => Position::factory(),
                'specialty_id' => Specialty::factory(),
                'type' => AssignmentRecordType::PRIMARY,
            ])
            ->for($user = User::factory()->unassigned()->createQuietly())
            ->create();

        $user = $user->fresh();

        $this->assertSame($assignment->unit->getKey(), $user->unit->getKey());
        $this->assertSame($assignment->position->getKey(), $user->position->getKey());
        $this->assertSame($assignment->specialty->getKey(), $user->specialty->getKey());
        $this->assertSame($assignment->status->getKey(), $user->status->getKey());
    }

    public function test_create_primary_assignment_record_assigns_user_properties_using_unit_slot(): void
    {
        $slot = Slot::factory()->createQuietly();
        $unit = Unit::factory()->hasAttached($slot)->createQuietly();
        $unitSlot = $unit->slots->first()->pivot->id;

        $assignment = AssignmentRecord::factory()
            ->state([
                'unit_id' => null,
                'position_id' => null,
                'specialty_id' => null,
                'status_id' => null,
                'unit_slot_id' => $unitSlot,
                'text' => 'BLAH',
                'type' => AssignmentRecordType::PRIMARY,
            ])
            ->for($user = User::factory()->unassigned()->createQuietly())
            ->create();

        $user = $user->fresh();

        $this->assertSame($unitSlot, $user->unit_slot_id);
        $this->assertSame($assignment->unit->getKey(), $user->unit->getKey());
        $this->assertSame($slot->position->getKey(), $user->position->getKey());
        $this->assertSame($slot->specialty->getKey(), $user->specialty->getKey());
    }

    public function test_create_primary_assignment_record_removes_user_properties(): void
    {
        $user = User::factory()->unassigned()->createQuietly();

        $this->assertTrue(blank($user->unit));
        $this->assertTrue(blank($user->position));
        $this->assertTrue(blank($user->unit));
        $this->assertTrue(blank($user->status));

        AssignmentRecord::factory()
            ->state([
                'status_id' => null,
                'unit_id' => null,
                'position_id' => null,
                'specialty_id' => null,
                'type' => AssignmentRecordType::PRIMARY,
            ])
            ->for($user)
            ->create();

        $user = $user->fresh();

        $this->assertNull($user->unit);
        $this->assertNull($user->position);
        $this->assertNull($user->specialty);
        $this->assertNull($user->status);
    }

    public function test_create_secondary_assignment_record_does_not_assign_user_properties(): void
    {
        $user = User::factory()->createQuietly();

        $assignment = AssignmentRecord::factory()
            ->state([
                'status_id' => Status::factory(),
                'unit_id' => Unit::factory(),
                'position_id' => Position::factory(),
                'specialty_id' => Specialty::factory(),
                'type' => AssignmentRecordType::SECONDARY,
            ])
            ->for($user)
            ->create();

        $user = $user->fresh();

        $this->assertNotSame($assignment->unit->getKey(), $user->unit->getKey());
        $this->assertNotSame($assignment->position->getKey(), $user->position->getKey());
        $this->assertNotSame($assignment->specialty->getKey(), $user->specialty->getKey());
        $this->assertNotSame($assignment->status->getKey(), $user->status->getKey());
    }
}
