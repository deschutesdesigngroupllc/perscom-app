<?php

namespace Tests\Feature\Tenant\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Events\EventsController;
use App\Models\Calendar;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'events';
    }

    public function controller(): string
    {
        return EventsController::class;
    }

    public function model(): string
    {
        return Event::class;
    }

    public function factory(): Factory
    {
        return Event::factory();
    }

    public function scopes(): array
    {
        return [
            'index' => 'view:event',
            'show' => 'view:event',
            'store' => 'create:event',
            'update' => 'update:event',
            'delete' => 'delete:event',
        ];
    }

    public function storeData(): array
    {
        return [
            'calendar_id' => Calendar::factory()->create()->getKey(),
            'name' => $this->faker->word,
            'start' => now(),
            'end' => now()->addDay(),
            'all_day' => false,
        ];
    }

    public function updateData(): array
    {
        return [
            'description' => $this->faker->paragraph,
        ];
    }
}
