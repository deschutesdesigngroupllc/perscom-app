<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Controllers\Api\Slots\SlotsController;
use App\Models\Slot;
use App\Models\Specialty;
use Illuminate\Database\Eloquent\Factories\Factory;

class SlotTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'slots';
    }

    public function controller(): string
    {
        return SlotsController::class;
    }

    public function model(): string
    {
        return Slot::class;
    }

    /**
     * @return Factory<Specialty>
     */
    public function factory(): Factory
    {
        return Slot::factory();
    }

    /**
     * @return string[]
     */
    public function scopes(): array
    {
        return [
            'index' => 'view:slot',
            'show' => 'view:slot',
            'store' => 'create:slot',
            'update' => 'update:slot',
            'delete' => 'delete:slot',
        ];
    }

    /**
     * @return string[]
     */
    public function storeData(): array
    {
        return [
            'name' => 'Test Slot',
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
