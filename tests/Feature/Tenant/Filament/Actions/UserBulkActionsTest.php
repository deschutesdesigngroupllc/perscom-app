<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\Actions;

use App\Filament\App\Resources\UserResource\Pages\ListUsers;
use App\Models\Award;
use App\Models\Enums\RankRecordType;
use App\Models\Permission;
use App\Models\Rank;
use App\Models\Unit;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class UserBulkActionsTest extends TenantTestCase
{
    public function test_promote_creates_a_rank_record_for_each_selected_user(): void
    {
        $this->actingAsSuperAdmin();
        $members = User::factory()->count(2)->createQuietly();
        $rank = Rank::factory()->createQuietly();

        Livewire::test(ListUsers::class)
            ->selectTableRecords($members->modelKeys())
            ->callAction(TestAction::make('promote')->table()->bulk(), [
                'rank_id' => $rank->getKey(),
                'type' => RankRecordType::PROMOTION->value,
            ])
            ->assertHasNoActionErrors()
            ->assertNotified('2 record(s) were created.');

        $members->each(fn (User $member) => $this->assertDatabaseHas('records_ranks', [
            'user_id' => $member->getKey(),
            'rank_id' => $rank->getKey(),
            'type' => RankRecordType::PROMOTION->value,
        ]));
    }

    public function test_assign_creates_an_assignment_record_for_each_selected_user(): void
    {
        $this->actingAsSuperAdmin();
        $member = User::factory()->createQuietly();
        $unit = Unit::factory()->createQuietly();

        Livewire::test(ListUsers::class)
            ->selectTableRecords([$member->getKey()])
            ->callAction(TestAction::make('assign')->table()->bulk(), [
                'unit_id' => $unit->getKey(),
                'type' => 'primary',
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('records_assignments', ['user_id' => $member->getKey(), 'unit_id' => $unit->getKey()]);
    }

    public function test_award_creates_an_award_record_for_each_selected_user(): void
    {
        $this->actingAsSuperAdmin();
        $member = User::factory()->createQuietly();
        $award = Award::factory()->createQuietly();

        Livewire::test(ListUsers::class)
            ->selectTableRecords([$member->getKey()])
            ->callAction(TestAction::make('award')->table()->bulk(), [
                'award_id' => $award->getKey(),
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('records_awards', ['user_id' => $member->getKey(), 'award_id' => $award->getKey()]);
    }

    public function test_bulk_actions_are_hidden_without_permission_to_create_the_records(): void
    {
        $user = User::factory()->withoutRoles()->createQuietly();
        $user->givePermissionTo(Permission::findOrCreate('view_any_user'), Permission::findOrCreate('view_user'));
        $this->actingAs($user);

        Livewire::test(ListUsers::class)
            ->assertActionHidden(TestAction::make('promote')->table()->bulk())
            ->assertActionHidden(TestAction::make('assign')->table()->bulk())
            ->assertActionHidden(TestAction::make('award')->table()->bulk());
    }

    private function actingAsSuperAdmin(): User
    {
        $user = User::factory()->createQuietly();
        $user->assignRole(Utils::getSuperAdminName());

        $this->actingAs($user);

        return $user;
    }
}
