<?php

namespace Tests\Feature\Tenant\Http\Controllers\Api\V1;

use App\Models\Award;
use App\Models\AwardRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class AwardRecordTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'award-records';
    }

    public function table(): string
    {
        return 'records_awards';
    }

    public function model(): string
    {
        return AwardRecord::class;
    }

    public function factory(): Factory
    {
        return AwardRecord::factory()->state([
            'user_id' => $this->user->getKey(),
        ]);
    }

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

    public function storeData(): array
    {
        return [
            'user_id' => $this->user->getKey(),
            'author_id' => $this->user->getKey(),
            'award_id' => Award::factory()->create()->getKey(),
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
