<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Services;

use App\Models\AssignmentRecord;
use App\Models\AwardRecord;
use App\Models\CombatRecord;
use App\Models\Position;
use App\Models\QualificationRecord;
use App\Models\RankRecord;
use App\Models\ServiceRecord;
use App\Models\Specialty;
use App\Models\Status;
use App\Models\Unit;
use App\Models\User;
use App\Services\ModelTextParserService;
use Tests\Feature\Tenant\TenantTestCase;

class ModelTextParserServiceTest extends TenantTestCase
{
    public function test_it_correctly_replaces_user_text(): void
    {
        $content = '{user_name} {user_email} {user_email_verified_at} {user_status} {user_assignment_position} {user_assignment_specialty} {user_assignment_unit} {user_rank}';

        $user = User::factory()->createQuietly();

        $replaced = ModelTextParserService::parse($content, $user);

        $this->assertSame($replaced, "$user->name $user->email {$user->email_verified_at->toDayDateTimeString()} {$user->status->name} {$user->position->name} {$user->specialty->name} {$user->unit->name} {$user->rank->name}");
    }

    public function test_it_correctly_replaces_assignment_record_text(): void
    {
        $content = '{assignment_record_status} {assignment_record_unit} {assignment_record_position} {assignment_record_speciality} {assignment_record_text} {assignment_record_date}';

        $record = AssignmentRecord::factory()->state([
            'user_id' => User::factory(),
            'status_id' => Status::factory(),
            'unit_id' => Unit::factory(),
            'position_id' => Position::factory(),
            'specialty_id' => Specialty::factory(),
        ])->createQuietly();

        $replaced = ModelTextParserService::parse(
            content: $content,
            attachedModel: $record
        );

        $this->assertSame($replaced, "{$record->status->name} {$record->unit->name} {$record->position->name} {$record->specialty->name} $record->text {$record->created_at->toDayDateTimeString()}");
    }

    public function test_it_correctly_replaces_award_record_text(): void
    {
        $content = '{award_record_award} {award_record_text} {award_record_date}';

        $record = AwardRecord::factory()->createQuietly();

        $replaced = ModelTextParserService::parse(
            content: $content,
            attachedModel: $record
        );

        $this->assertSame($replaced, "{$record->award->name} $record->text {$record->created_at->toDayDateTimeString()}");
    }

    public function test_it_correctly_replaces_combat_record_text(): void
    {
        $content = '{combat_record_text} {combat_record_date}';

        $record = CombatRecord::factory()->createQuietly();

        $replaced = ModelTextParserService::parse(
            content: $content,
            attachedModel: $record
        );

        $this->assertSame($replaced, "$record->text {$record->created_at->toDayDateTimeString()}");
    }

    public function test_it_correctly_replaces_qualification_record_text(): void
    {
        $content = '{qualification_record_qualification} {qualification_record_text} {qualification_record_date}';

        $record = QualificationRecord::factory()->createQuietly();

        $replaced = ModelTextParserService::parse(
            content: $content,
            attachedModel: $record
        );

        $this->assertSame($replaced, "{$record->qualification->name} $record->text {$record->created_at->toDayDateTimeString()}");
    }

    public function test_it_correctly_replaces_rank_record_text(): void
    {
        $content = '{rank_record_rank} {rank_record_type} {rank_record_text} {rank_record_date}';

        $record = RankRecord::factory()->createQuietly();

        $replaced = ModelTextParserService::parse(
            content: $content,
            attachedModel: $record
        );

        $this->assertSame($replaced, "{$record->rank->name} {$record->type->getLabel()} $record->text {$record->created_at->toDayDateTimeString()}");
    }

    public function test_it_correctly_replaces_service_record_text(): void
    {
        $content = '{service_record_text} {service_record_date}';

        $record = ServiceRecord::factory()->createQuietly();

        $replaced = ModelTextParserService::parse(
            content: $content,
            attachedModel: $record
        );

        $this->assertSame($replaced, "$record->text {$record->created_at->toDayDateTimeString()}");
    }

    public function test_it_correctly_replaces_author_text(): void
    {
        $content = '{author_resource_name}';

        $record = ServiceRecord::factory()->createQuietly();

        $replaced = ModelTextParserService::parse(
            content: $content,
            attachedModel: $record
        );

        $this->assertSame($replaced, $record->author->name);
    }
}
