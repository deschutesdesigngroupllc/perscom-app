<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Controllers\Api\AwardRecords\AwardRecordsController;
use App\Models\Award;
use App\Models\AwardRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class AwardRecordTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'award-records';
    }

    public function controller(): string
    {
        return AwardRecordsController::class;
    }

    public function model(): string
    {
        return AwardRecord::class;
    }

    /**
     * @return Factory<AwardRecord>
     */
    public function factory(): Factory
    {
        return AwardRecord::factory()->state([
            'user_id' => $this->user->getKey(),
        ]);
    }

    /**
     * @return string[]
     */
    public function scopes(): array
    {
        return [
            'index' => 'view:awardrecord',
            'show' => 'view:awardrecord',
            'store' => 'create:awardrecord',
            'update' => 'update:awardrecord',
            'delete' => 'delete:awardrecord',
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
            'award_id' => Award::factory()->create()->getKey(),
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
