<?php

namespace Tests\Feature\Tenant\Http\Controllers\Api\V1;

use App\Models\ServiceRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceRecordTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'service-records';
    }

    public function model(): string
    {
        return ServiceRecord::class;
    }

    public function factory(): Factory
    {
        return ServiceRecord::factory();
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
            'name' => 'Test Task',
            'description' => $this->faker->sentence,
            'instructions' => $this->faker->sentence,
        ];
    }

    public function updateData(): array
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
