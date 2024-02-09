<?php

namespace Tests\Feature\Tenant\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\RankRecords\RankRecordsController;
use App\Models\Enums\RankRecordType;
use App\Models\Rank;
use App\Models\RankRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class RankRecordTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'rank-records';
    }

    public function controller(): string
    {
        return RankRecordsController::class;
    }

    public function model(): string
    {
        return RankRecord::class;
    }

    public function factory(): Factory
    {
        return RankRecord::factory()->state([
            'user_id' => $this->user->getKey(),
        ]);
    }

    public function scopes(): array
    {
        return [
            'index' => 'view:rankrecord',
            'show' => 'view:rankrecord',
            'store' => 'create:rankrecord',
            'update' => 'update:rankrecord',
            'delete' => 'delete:rankrecord',
        ];
    }

    public function storeData(): array
    {
        return [
            'user_id' => $this->user->getKey(),
            'author_id' => $this->user->getKey(),
            'rank_id' => Rank::factory()->create()->getKey(),
            'type' => RankRecordType::PROMOTION,
            'text' => $this->faker->paragraph,
        ];
    }

    public function updateData(): array
    {
        return [
            'text' => $this->faker->paragraph,
        ];
    }
}
