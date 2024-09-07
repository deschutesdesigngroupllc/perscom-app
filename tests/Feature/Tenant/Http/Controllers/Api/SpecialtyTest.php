<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Controllers\Api\Specialties\SpecialtiesController;
use App\Models\Specialty;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpecialtyTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'specialties';
    }

    public function controller(): string
    {
        return SpecialtiesController::class;
    }

    public function model(): string
    {
        return Specialty::class;
    }

    public function factory(): Factory
    {
        return Specialty::factory();
    }

    public function scopes(): array
    {
        return [
            'index' => 'view:specialty',
            'show' => 'view:specialty',
            'store' => 'create:specialty',
            'update' => 'update:specialty',
            'delete' => 'delete:specialty',
        ];
    }

    public function storeData(): array
    {
        return [
            'name' => 'Test Specialty',
            'description' => $this->faker->sentence,
        ];
    }

    public function updateData(): array
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
