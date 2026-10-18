<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Ai\Tools\Proposals;

use App\Ai\Tools\Proposals\ProposeAssignmentRecords;
use App\Ai\Tools\Proposals\ProposeAwardRecords;
use App\Ai\Tools\Proposals\ProposeMessage;
use App\Ai\Tools\Proposals\ProposeQualificationRecords;
use App\Ai\Tools\Proposals\ProposeRankRecords;
use App\Ai\Tools\Proposals\ProposeStatusChanges;
use App\Data\Copilot\ProposedChangeSetData;
use App\Models\Award;
use App\Models\Enums\AssignmentRecordType;
use App\Models\Enums\CopilotOperationType;
use App\Models\Enums\NotificationChannel;
use App\Models\Enums\RankRecordType;
use App\Models\Position;
use App\Models\Qualification;
use App\Models\Rank;
use App\Models\Status;
use App\Models\Unit;
use App\Models\User;
use Laravel\Ai\Tools\Request;
use Tests\Feature\Tenant\TenantTestCase;

class ProposalToolTest extends TenantTestCase
{
    public function test_rank_record_proposals_queue_an_operation_without_writing(): void
    {
        $changeSet = new ProposedChangeSetData;
        $member = User::factory()->createQuietly(['name' => 'Alex Mason']);
        $rank = Rank::factory()->createQuietly(['name' => 'Corporal']);

        $result = (string) new ProposeRankRecords($changeSet)->handle(new Request([
            'user_ids' => [$member->getKey()],
            'rank_id' => $rank->getKey(),
            'type' => RankRecordType::PROMOTION->value,
        ]));

        $this->assertStringContainsString('nothing has been changed yet', $result);
        $this->assertCount(1, $changeSet->operations);
        $this->assertSame(CopilotOperationType::RANK_RECORD, $changeSet->operations[0]->type);
        $this->assertSame('Promotion of Alex Mason to Corporal', $changeSet->operations[0]->summary);
        $this->assertDatabaseMissing('records_ranks', ['user_id' => $member->getKey()]);
    }

    public function test_assignment_record_proposals_include_position_and_type(): void
    {
        $changeSet = new ProposedChangeSetData;
        $member = User::factory()->createQuietly();
        $unit = Unit::factory()->createQuietly(['name' => '2nd Platoon']);
        $position = Position::factory()->createQuietly(['name' => 'Rifleman']);

        new ProposeAssignmentRecords($changeSet)->handle(new Request([
            'user_ids' => [$member->getKey()],
            'unit_id' => $unit->getKey(),
            'position_id' => $position->getKey(),
            'type' => AssignmentRecordType::SECONDARY->value,
        ]));

        $operation = $changeSet->operations[0];

        $this->assertSame($unit->getKey(), $operation->targetId);
        $this->assertSame(['type' => 'secondary', 'position_id' => $position->getKey()], $operation->attributes);
        $this->assertStringContainsString('to 2nd Platoon as Rifleman', $operation->summary);
        $this->assertDatabaseMissing('records_assignments', ['user_id' => $member->getKey()]);
    }

    public function test_award_qualification_and_status_proposals_queue_operations(): void
    {
        $changeSet = new ProposedChangeSetData;
        $member = User::factory()->createQuietly();

        new ProposeAwardRecords($changeSet)->handle(new Request(['user_ids' => [$member->getKey()], 'award_id' => Award::factory()->createQuietly()->getKey()]));
        new ProposeQualificationRecords($changeSet)->handle(new Request(['user_ids' => [$member->getKey()], 'qualification_id' => Qualification::factory()->createQuietly()->getKey()]));
        new ProposeStatusChanges($changeSet)->handle(new Request(['user_ids' => [$member->getKey()], 'status_id' => Status::factory()->createQuietly()->getKey()]));

        $this->assertSame(
            [CopilotOperationType::AWARD_RECORD, CopilotOperationType::QUALIFICATION_RECORD, CopilotOperationType::STATUS_RECORD],
            collect($changeSet->operations)->pluck('type')->all(),
        );
        $this->assertDatabaseMissing('records_awards', ['user_id' => $member->getKey()]);
        $this->assertDatabaseMissing('records_qualifications', ['user_id' => $member->getKey()]);
    }

    public function test_message_proposals_default_to_database_and_mail(): void
    {
        $changeSet = new ProposedChangeSetData;
        $member = User::factory()->createQuietly();

        new ProposeMessage($changeSet)->handle(new Request(['user_ids' => [$member->getKey()], 'message' => 'Drill moved to Friday.']));

        $this->assertSame([NotificationChannel::DATABASE->value, NotificationChannel::MAIL->value], $changeSet->operations[0]->attributes['channels']);
        $this->assertDatabaseMissing('messages', ['message' => 'Drill moved to Friday.']);
    }

    public function test_invalid_proposals_are_reported_and_not_queued(): void
    {
        $changeSet = new ProposedChangeSetData;

        $result = (string) new ProposeRankRecords($changeSet)->handle(new Request([
            'user_ids' => [999999],
            'rank_id' => 999999,
        ]));

        $this->assertStringStartsWith('The proposal was not added:', $result);
        $this->assertTrue($changeSet->isEmpty());
    }
}
