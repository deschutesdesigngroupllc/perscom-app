<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Actions\Copilot;

use App\Actions\Copilot\ApplyChangeSet;
use App\Data\Copilot\ProposedChangeSetData;
use App\Data\Copilot\ProposedOperationData;
use App\Events\Automations\RecordCreated;
use App\Models\Activity;
use App\Models\Award;
use App\Models\Enums\CopilotOperationType;
use App\Models\Enums\RankRecordType;
use App\Models\Message;
use App\Models\Permission;
use App\Models\Qualification;
use App\Models\Rank;
use App\Models\Status;
use App\Models\Unit;
use App\Models\User;
use App\Notifications\Tenant\NewRankRecord;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Tests\Feature\Tenant\TenantTestCase;

class ApplyChangeSetTest extends TenantTestCase
{
    public function test_it_creates_records_for_every_operation_and_member(): void
    {
        $actor = $this->superAdmin();
        $members = User::factory()->count(2)->createQuietly();
        $rank = Rank::factory()->createQuietly();
        $unit = Unit::factory()->createQuietly();
        $award = Award::factory()->createQuietly();
        $qualification = Qualification::factory()->createQuietly();
        $status = Status::factory()->createQuietly();
        $ids = $members->modelKeys();

        $result = ApplyChangeSet::handle(new ProposedChangeSetData([
            new ProposedOperationData(CopilotOperationType::RANK_RECORD, $ids, $rank->getKey(), 'Well earned.', ['type' => RankRecordType::PROMOTION->value], 'Promote'),
            new ProposedOperationData(CopilotOperationType::ASSIGNMENT_RECORD, $ids, $unit->getKey(), summary: 'Assign'),
            new ProposedOperationData(CopilotOperationType::AWARD_RECORD, $ids, $award->getKey(), summary: 'Award'),
            new ProposedOperationData(CopilotOperationType::QUALIFICATION_RECORD, $ids, $qualification->getKey(), summary: 'Qualify'),
            new ProposedOperationData(CopilotOperationType::STATUS_RECORD, $ids, $status->getKey(), 'On leave.', summary: 'Status'),
            new ProposedOperationData(CopilotOperationType::MESSAGE, $ids, text: 'Formation at 1900.', summary: 'Message'),
        ]), $actor);

        $this->assertSame(11, $result['created']);
        $this->assertCount(6, $result['operations']);

        $members->each(function (User $member) use ($actor, $rank, $unit, $award, $qualification, $status): void {
            $this->assertDatabaseHas('records_ranks', ['user_id' => $member->getKey(), 'rank_id' => $rank->getKey(), 'author_id' => $actor->getKey(), 'text' => 'Well earned.']);
            $this->assertDatabaseHas('records_assignments', ['user_id' => $member->getKey(), 'unit_id' => $unit->getKey()]);
            $this->assertDatabaseHas('records_awards', ['user_id' => $member->getKey(), 'award_id' => $award->getKey()]);
            $this->assertDatabaseHas('records_qualifications', ['user_id' => $member->getKey(), 'qualification_id' => $qualification->getKey()]);
            $this->assertSame($status->getKey(), $member->fresh()->status_id);
            $this->assertSame($rank->getKey(), $member->fresh()->rank_id);
        });

        $this->assertSame('Formation at 1900.', Message::query()->sole()->message);
    }

    public function test_it_fires_the_usual_notifications_and_automation_events(): void
    {
        Notification::fake();
        Event::fake([RecordCreated::class]);

        $actor = $this->superAdmin();
        $member = User::factory()->createQuietly();
        $rank = Rank::factory()->createQuietly();

        ApplyChangeSet::handle(new ProposedChangeSetData([
            new ProposedOperationData(CopilotOperationType::RANK_RECORD, [$member->getKey()], $rank->getKey(), summary: 'Promote'),
        ]), $actor);

        Notification::assertSentTo($member, NewRankRecord::class);
        Event::assertDispatched(RecordCreated::class);
    }

    public function test_it_logs_that_the_changes_came_from_the_copilot(): void
    {
        $actor = $this->superAdmin();
        $member = User::factory()->createQuietly();
        $award = Award::factory()->createQuietly();

        ApplyChangeSet::handle(new ProposedChangeSetData([
            new ProposedOperationData(CopilotOperationType::AWARD_RECORD, [$member->getKey()], $award->getKey(), summary: 'Award the medal'),
        ]), $actor);

        $activity = Activity::query()->where('log_name', 'copilot')->sole();

        $this->assertSame($actor->getKey(), (int) $activity->causer_id);
        $this->assertSame(['Award the medal'], $activity->properties->get('operations'));
    }

    public function test_it_rejects_unauthorized_operations_without_writing_anything(): void
    {
        $actor = User::factory()->withoutRoles()->createQuietly();
        $actor->givePermissionTo(Permission::findOrCreate('create_award_record'));

        $member = User::factory()->createQuietly();
        $award = Award::factory()->createQuietly();
        $rank = Rank::factory()->createQuietly();

        try {
            ApplyChangeSet::handle(new ProposedChangeSetData([
                new ProposedOperationData(CopilotOperationType::AWARD_RECORD, [$member->getKey()], $award->getKey(), summary: 'Award'),
                new ProposedOperationData(CopilotOperationType::RANK_RECORD, [$member->getKey()], $rank->getKey(), summary: 'Promote'),
            ]), $actor);

            $this->fail('The change set should not have been applied.');
        } catch (AuthorizationException) {
            $this->assertDatabaseMissing('records_awards', ['user_id' => $member->getKey()]);
            $this->assertDatabaseMissing('records_ranks', ['user_id' => $member->getKey()]);
        }
    }

    public function test_it_authorizes_status_changes_against_each_member(): void
    {
        $actor = User::factory()->withoutRoles()->createQuietly();
        $member = User::factory()->createQuietly();
        $status = Status::factory()->createQuietly();

        $this->expectException(AuthorizationException::class);

        ApplyChangeSet::handle(new ProposedChangeSetData([
            new ProposedOperationData(CopilotOperationType::STATUS_RECORD, [$member->getKey()], $status->getKey(), summary: 'Status'),
        ]), $actor);
    }

    public function test_it_rejects_operations_that_point_at_missing_records(): void
    {
        $actor = $this->superAdmin();
        $member = User::factory()->createQuietly();

        $this->expectException(ValidationException::class);

        ApplyChangeSet::handle(new ProposedChangeSetData([
            new ProposedOperationData(CopilotOperationType::RANK_RECORD, [$member->getKey()], 999999, summary: 'Promote'),
        ]), $actor);
    }

    public function test_it_rejects_operations_for_members_that_do_not_exist(): void
    {
        $actor = $this->superAdmin();
        $rank = Rank::factory()->createQuietly();

        $this->expectException(ValidationException::class);

        ApplyChangeSet::handle(new ProposedChangeSetData([
            new ProposedOperationData(CopilotOperationType::RANK_RECORD, [999999], $rank->getKey(), summary: 'Promote'),
        ]), $actor);
    }

    private function superAdmin(): User
    {
        $user = User::factory()->createQuietly();
        $user->assignRole(Utils::getSuperAdminName());

        return $user;
    }
}
