<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Controllers\Api\Qualifications\QualificationsController;
use App\Models\Qualification;
use Illuminate\Database\Eloquent\Factories\Factory;

class QualificationTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'qualifications';
    }

    public function controller(): string
    {
        return QualificationsController::class;
    }

    public function model(): string
    {
        return Qualification::class;
    }

    /**
     * @return Factory<Qualification>
     */
    public function factory(): Factory
    {
        return Qualification::factory();
    }

    /**
     * @return string[]
     */
    public function scopes(): array
    {
        return [
            'index' => 'view:qualification',
            'show' => 'view:qualification',
            'store' => 'create:qualification',
            'update' => 'update:qualification',
            'delete' => 'delete:qualification',
        ];
    }

    /**
     * @return string[]
     */
    public function storeData(): array
    {
        return [
            'name' => 'Test Qualification',
            'description' => $this->faker->sentence,
        ];
    }

    /**
     * @return string[]
     */
    public function updateData(): array
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
