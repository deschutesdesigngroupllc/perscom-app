<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Controllers\Api\ServiceRecords\ServiceRecordsController;
use App\Models\ServiceRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceRecordTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'service-records';
    }

    public function controller(): string
    {
        return ServiceRecordsController::class;
    }

    public function model(): string
    {
        return ServiceRecord::class;
    }

    public function factory(): Factory
    {
        return ServiceRecord::factory()->state([
            'user_id' => $this->user->getKey(),
        ]);
    }

    public function scopes(): array
    {
        return [
            'index' => 'view:servicerecord',
            'show' => 'view:servicerecord',
            'store' => 'create:servicerecord',
            'update' => 'update:servicerecord',
            'delete' => 'delete:servicerecord',
        ];
    }

    public function storeData(): array
    {
        return [
            'user_id' => $this->user->getKey(),
            'author_id' => $this->user->getKey(),
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
