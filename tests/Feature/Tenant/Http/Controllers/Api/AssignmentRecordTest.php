<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Controllers\Api\AssignmentRecords\AssignmentRecordsController;
use App\Models\AssignmentRecord;
use App\Models\Enums\AssignmentRecordType;
use App\Models\Position;
use App\Models\Specialty;
use App\Models\Status;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssignmentRecordTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'assignment-records';
    }

    public function controller(): string
    {
        return AssignmentRecordsController::class;
    }

    public function model(): string
    {
        return AssignmentRecord::class;
    }

    /**
     * @return Factory<AssignmentRecord>
     */
    public function factory(): Factory
    {
        return AssignmentRecord::factory()->state([
            'user_id' => $this->user->getKey(),
        ]);
    }

    /**
     * @return string[]
     */
    public function scopes(): array
    {
        return [
            'index' => 'view:assignmentrecord',
            'show' => 'view:assignmentrecord',
            'store' => 'create:assignmentrecord',
            'update' => 'update:assignmentrecord',
            'delete' => 'delete:assignmentrecord',
        ];
    }

    /**
     * @return string[]
     */
    public function storeData(): array
    {
        return [
            'user_id' => $this->user->getKey(),
            'author_id' => $this->user->getKey(),
            'position_id' => Position::factory()->create()->getKey(),
            'unit_id' => Unit::factory()->create()->getKey(),
            'specialty_id' => Specialty::factory()->create()->getKey(),
            'status_id' => Status::factory()->create()->getKey(),
            'type' => AssignmentRecordType::PRIMARY->value,
            'text' => $this->faker->paragraph,
        ];
    }

    /**
     * @return string[]
     */
    public function updateData(): array
    {
        return [
            'text' => $this->faker->paragraph,
        ];
    }
}
