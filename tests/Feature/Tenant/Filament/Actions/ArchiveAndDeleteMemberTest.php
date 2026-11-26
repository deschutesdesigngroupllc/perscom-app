<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\Actions;

use App\Filament\App\Resources\UserResource\Pages\ViewUser;
use App\Models\AwardRecord;
use App\Models\Status;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class ArchiveAndDeleteMemberTest extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::factory()->createQuietly();
        $admin->assignRole(Utils::getSuperAdminName());
        $this->actingAs($admin);
    }

    public function test_archiving_changes_status_and_keeps_the_member_and_history(): void
    {
        Status::factory()->createQuietly(['name' => 'Active']);
        $discharged = Status::factory()->createQuietly(['name' => 'Honorably Discharged']);
        $member = User::factory()->createQuietly();
        $record = AwardRecord::factory()->for($member)->createQuietly();

        Livewire::test(ViewUser::class, ['record' => $member->getKey()])
            ->mountAction('archiveMember')
            ->assertSchemaStateSet(['status_id' => $discharged->getKey()])
            ->setActionData(['text' => 'End of service.'])
            ->callMountedAction()
            ->assertHasNoActionErrors();

        $this->assertSame($discharged->getKey(), $member->refresh()->status_id);
        $this->assertModelExists($member);
        $this->assertModelExists($record);
        $this->assertDatabaseHas('model_has_statuses', [
            'model_id' => $member->getKey(),
            'status_id' => $discharged->getKey(),
            'text' => 'End of service.',
        ]);
    }

    public function test_deleting_requires_typing_the_members_name(): void
    {
        $member = User::factory()->createQuietly(['name' => 'Jordan Reyes']);

        Livewire::test(ViewUser::class, ['record' => $member->getKey()])
            ->callAction('delete', ['confirmation' => 'jordan'])
            ->assertHasActionErrors(['confirmation' => 'in']);

        $this->assertModelExists($member);
    }

    public function test_the_delete_confirmation_states_how_many_records_will_be_lost(): void
    {
        $member = User::factory()->createQuietly(['name' => 'Jordan Reyes']);
        AwardRecord::factory()->count(2)->for($member)->createQuietly();

        Livewire::test(ViewUser::class, ['record' => $member->getKey()])
            ->mountAction('delete')
            ->assertMountedActionModalSee('2 records in their service history')
            ->assertMountedActionModalSee('archive them instead');
    }

    public function test_typing_the_name_deletes_the_member(): void
    {
        $member = User::factory()->createQuietly(['name' => 'Jordan Reyes']);

        Livewire::test(ViewUser::class, ['record' => $member->getKey()])
            ->callAction('delete', ['confirmation' => 'Jordan Reyes'])
            ->assertHasNoErrors();

        $this->assertModelMissing($member);
    }
}
