<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Services;

use App\Models\AssignmentRecord;
use App\Models\Position;
use App\Models\Specialty;
use App\Models\Status;
use App\Models\Unit;
use App\Models\User;
use App\Services\ModelTextParserService;
use Tests\Feature\Tenant\TenantTestCase;

class ModelTextParserServiceTest extends TenantTestCase
{
    public function test_it_correctly_replaces_user_text()
    {
        $content = '{user_name} {user_email} {user_email_verified_at} {user_status} {user_assignment_position} {user_assignment_specialty} {user_assignment_unit} {user_rank}';

        $user = User::factory()->createQuietly();

        $replaced = ModelTextParserService::parse($content, $user);

        $this->assertSame($replaced, "$user->name $user->email {$user->email_verified_at->toDayDateTimeString()} {$user->status->name} {$user->position->name} {$user->specialty->name} {$user->unit->name} {$user->rank->name}");
    }

    public function test_it_correctly_replaces_model_text()
    {
        $content = '{assignment_record_status} {assignment_record_unit} {assignment_record_position} {assignment_record_speciality} {assignment_record_text} {assignment_record_date}';

        $assignmentRecord = AssignmentRecord::factory()->state([
            'user_id' => User::factory(),
            'status_id' => Status::factory(),
            'unit_id' => Unit::factory(),
            'position_id' => Position::factory(),
            'specialty_id' => Specialty::factory(),
        ])->createQuietly();

        $replaced = ModelTextParserService::parse(
            content: $content,
            attachedModel: $assignmentRecord
        );

        $this->assertSame($replaced, "{$assignmentRecord->status->name} {$assignmentRecord->unit->name} {$assignmentRecord->position->name} {$assignmentRecord->specialty->name} $assignmentRecord->text {$assignmentRecord->created_at->toDayDateTimeString()}");
    }
}
