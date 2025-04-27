<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Controllers\Api\Competencies\CompetenciesController;
use App\Models\Competency;
use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompetencyTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'competencies';
    }

    public function controller(): string
    {
        return CompetenciesController::class;
    }

    public function model(): string
    {
        return Competency::class;
    }

    /**
     * @return Factory<Group>
     */
    public function factory(): Factory
    {
        return Competency::factory();
    }

    /**
     * @return string[]
     */
    public function scopes(): array
    {
        return [
            'index' => 'view:competency',
            'show' => 'view:competency',
            'store' => 'create:competency',
            'update' => 'update:competency',
            'delete' => 'delete:competency',
        ];
    }

    /**
     * @return string[]
     */
    public function storeData(): array
    {
        return [
            'name' => 'Test Competency',
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
