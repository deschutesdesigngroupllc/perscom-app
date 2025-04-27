<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Controllers\Api\TrainingRecords\TrainingRecordsController;
use App\Models\CombatRecord;
use App\Models\TrainingRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainingRecordTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'training-records';
    }

    public function controller(): string
    {
        return TrainingRecordsController::class;
    }

    public function model(): string
    {
        return TrainingRecord::class;
    }

    /**
     * @return Factory<CombatRecord>
     */
    public function factory(): Factory
    {
        return TrainingRecord::factory()->state([
            'user_id' => $this->user->getKey(),
        ]);
    }

    /**
     * @return string[]
     */
    public function scopes(): array
    {
        return [
            'index' => 'view:trainingrecord',
            'show' => 'view:trainingrecord',
            'store' => 'create:trainingrecord',
            'update' => 'update:trainingrecord',
            'delete' => 'delete:trainingrecord',
        ];
    }

    /**
     * @return string[]
     */
    public function storeData(): array
    {
        return [
            'user_id' => $this->user->getKey(),
            'instructor_id' => $this->user->getKey(),
            'author_id' => $this->user->getKey(),
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
