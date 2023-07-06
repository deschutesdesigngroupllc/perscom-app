<?php

namespace Tests\Feature\Tenant\Traits;

use App\Jobs\GenerateOpenAiNewsfeedContent;
use App\Models\AssignmentRecord;
use App\Models\AwardRecord;
use App\Models\CombatRecord;
use App\Models\QualificationRecord;
use App\Models\RankRecord;
use App\Models\ServiceRecord;
use Illuminate\Support\Facades\Queue;
use Tests\Feature\Tenant\TenantTestCase;

class LogsActivityTest extends TenantTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Queue::fake([GenerateOpenAiNewsfeedContent::class]);
    }

    public function test_assignment_record_logs_newsfeed_activity()
    {
        $record = AssignmentRecord::factory()->create();

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'newsfeed',
            'subject_type' => AssignmentRecord::class,
            'subject_id' => $record->id,
        ]);
    }

    public function test_award_record_logs_newsfeed_activity()
    {
        $record = AwardRecord::factory()->create();

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'newsfeed',
            'subject_type' => AwardRecord::class,
            'subject_id' => $record->id,
        ]);
    }

    public function test_combat_record_logs_newsfeed_activity()
    {
        $record = CombatRecord::factory()->create();

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'newsfeed',
            'subject_type' => CombatRecord::class,
            'subject_id' => $record->id,
        ]);
    }

    public function test_qualification_record_logs_newsfeed_activity()
    {
        $record = QualificationRecord::factory()->create();

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'newsfeed',
            'subject_type' => QualificationRecord::class,
            'subject_id' => $record->id,
        ]);
    }

    public function test_rank_record_logs_newsfeed_activity()
    {
        $record = RankRecord::factory()->create();

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'newsfeed',
            'subject_type' => RankRecord::class,
            'subject_id' => $record->id,
        ]);
    }

    public function test_service_record_logs_newsfeed_activity()
    {
        $record = ServiceRecord::factory()->create();

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'newsfeed',
            'subject_type' => ServiceRecord::class,
            'subject_id' => $record->id,
        ]);
    }
}
