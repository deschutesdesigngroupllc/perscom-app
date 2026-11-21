<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\Actions;

use App\Filament\App\Resources\UserResource\Pages\ListUsers;
use App\Filament\App\Resources\UserResource\Pages\ViewUser;
use App\Filament\App\Resources\UserResource\RelationManagers\AwardRecordsRelationManager;
use App\Models\Award;
use App\Models\Enums\RankRecordType;
use App\Models\Rank;
use App\Models\Unit;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class MemberRecordActionsTest extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::factory()->createQuietly();
        $admin->assignRole(Utils::getSuperAdminName());
        $this->actingAs($admin);
    }

    public function test_promote_from_the_profile_defaults_to_the_next_rank(): void
    {
        $senior = Rank::factory()->createQuietly(['order' => 1]);
        $junior = Rank::factory()->createQuietly(['order' => 2]);
        $member = User::factory()->createQuietly(['rank_id' => $junior->getKey()]);

        Livewire::test(ViewUser::class, ['record' => $member->getKey()])
            ->mountAction('promoteMember')
            ->assertSchemaStateSet([
                'rank_id' => $senior->getKey(),
                'type' => RankRecordType::PROMOTION,
            ])
            ->callMountedAction()
            ->assertHasNoActionErrors()
            ->assertNotified('Created 1 Record');

        $this->assertDatabaseHas('records_ranks', [
            'user_id' => $member->getKey(),
            'rank_id' => $senior->getKey(),
            'type' => RankRecordType::PROMOTION->value,
        ]);
    }

    public function test_award_from_a_members_row(): void
    {
        $member = User::factory()->createQuietly();
        $award = Award::factory()->createQuietly();

        Livewire::test(ListUsers::class)
            ->callAction(TestAction::make('awardMember')->table($member), ['award_id' => $award->getKey()])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('records_awards', ['user_id' => $member->getKey(), 'award_id' => $award->getKey()]);
    }

    public function test_award_from_the_profile_awards_tab_targets_that_member(): void
    {
        $member = User::factory()->createQuietly();
        $award = Award::factory()->createQuietly();

        Livewire::test(AwardRecordsRelationManager::class, ['ownerRecord' => $member, 'pageClass' => ViewUser::class])
            ->callAction(TestAction::make('awardMember')->table(), ['award_id' => $award->getKey()])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('records_awards', ['user_id' => $member->getKey(), 'award_id' => $award->getKey()]);
    }

    public function test_assign_from_the_profile(): void
    {
        $member = User::factory()->createQuietly();
        $unit = Unit::factory()->createQuietly();

        Livewire::test(ViewUser::class, ['record' => $member->getKey()])
            ->callAction('assignMember', ['unit_id' => $unit->getKey(), 'type' => 'primary'])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('records_assignments', ['user_id' => $member->getKey(), 'unit_id' => $unit->getKey()]);
    }
}
