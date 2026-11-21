<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Resources;

use App\Filament\App\Resources\SubmissionResource\Pages\EditSubmission;
use App\Filament\App\Resources\SubmissionResource\RelationManagers\StatusesRelationManager;
use App\Filament\App\Resources\UnitResource\Pages\EditUnit;
use App\Filament\App\Resources\UnitResource\RelationManagers\SlotsRelationManager;
use App\Models\Enums\RosterMode;
use App\Models\Slot;
use App\Models\Status;
use App\Models\Submission;
use App\Models\Unit;
use App\Models\User;
use App\Settings\DashboardSettings;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class DetachRelationManagersTest extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::factory()->createQuietly();
        $admin->assignRole(Utils::getSuperAdminName());
        $this->actingAs($admin);
    }

    public function test_removing_a_status_from_a_submission_keeps_the_status(): void
    {
        $submission = Submission::factory()->createQuietly();
        $status = Status::factory()->createQuietly();
        $submission->statuses()->attach($status);

        Livewire::test(StatusesRelationManager::class, ['ownerRecord' => $submission, 'pageClass' => EditSubmission::class])
            ->callAction(TestAction::make('detach')->table($status));

        $this->assertModelExists($status);
        $this->assertFalse($submission->statuses()->whereKey($status->getKey())->exists());
    }

    public function test_bulk_removing_statuses_from_a_submission_keeps_the_statuses(): void
    {
        $submission = Submission::factory()->createQuietly();
        $statuses = Status::factory()->count(2)->createQuietly();
        $submission->statuses()->attach($statuses);

        Livewire::test(StatusesRelationManager::class, ['ownerRecord' => $submission, 'pageClass' => EditSubmission::class])
            ->selectTableRecords($statuses)
            ->callAction(TestAction::make('detach')->table()->bulk());

        $statuses->each(fn (Status $status) => $this->assertModelExists($status));
        $this->assertSame(0, $submission->statuses()->count());
    }

    public function test_bulk_removing_slots_from_a_unit_keeps_the_slots(): void
    {
        DashboardSettings::fake(['roster_mode' => RosterMode::MANUAL]);

        $unit = Unit::factory()->createQuietly();
        $slots = Slot::factory()->count(2)->createQuietly();
        $unit->slots()->attach($slots);

        Livewire::test(SlotsRelationManager::class, ['ownerRecord' => $unit, 'pageClass' => EditUnit::class])
            ->selectTableRecords($unit->slots()->get()->map(fn (Slot $slot): string => (string) $slot->pivot->getKey())->all())
            ->assertActionVisible(TestAction::make('detach')->table()->bulk())
            ->callAction(TestAction::make('detach')->table()->bulk())
            ->assertNotified(__('filament-actions::detach.multiple.notifications.detached.title'));

        $slots->each(fn (Slot $slot) => $this->assertModelExists($slot));
        $this->assertSame(0, $unit->slots()->count());
    }
}
