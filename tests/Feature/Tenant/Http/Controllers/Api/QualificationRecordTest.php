<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Controllers\Api\QualificationRecords\QualificationRecordsController;
use App\Models\Qualification;
use App\Models\QualificationRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class QualificationRecordTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'qualification-records';
    }

    public function controller(): string
    {
        return QualificationRecordsController::class;
    }

    public function model(): string
    {
        return QualificationRecord::class;
    }

    /**
     * @return Factory<QualificationRecord>
     */
    public function factory(): Factory
    {
        return QualificationRecord::factory()->state([
            'user_id' => $this->user->getKey(),
        ]);
    }

    /**
     * @return string[]
     */
    public function scopes(): array
    {
        return [
            'index' => 'view:qualificationrecord',
            'show' => 'view:qualificationrecord',
            'store' => 'create:qualificationrecord',
            'update' => 'update:qualificationrecord',
            'delete' => 'delete:qualificationrecord',
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
            'qualification_id' => Qualification::factory()->create()->getKey(),
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
