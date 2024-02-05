<?php

namespace Tests\Feature\Tenant\Http\Controllers\Api\V1;

use App\Models\Qualification;
use App\Models\QualificationRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class QualificationRecordTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'qualification-records';
    }

    public function table(): string
    {
        return 'records_qualifications';
    }

    public function model(): string
    {
        return QualificationRecord::class;
    }

    public function factory(): Factory
    {
        return QualificationRecord::factory()->state([
            'user_id' => $this->user->getKey(),
        ]);
    }

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

    public function storeData(): array
    {
        return [
            'user_id' => $this->user->getKey(),
            'author_id' => $this->user->getKey(),
            'qualification_id' => Qualification::factory()->create()->getKey(),
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
