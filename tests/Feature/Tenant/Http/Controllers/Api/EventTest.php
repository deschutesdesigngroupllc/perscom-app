<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Controllers\Api\Events\EventsController;
use App\Models\Calendar;
use App\Models\Event;
use App\Models\User;
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

    /**
     * @return Factory<Event>
     */
    public function factory(): Factory
    {
        return Event::factory();
    }

    /**
     * @return string[]
     */
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

    /**
     * @return array<string, mixed>
     */
    public function storeData(): array
    {
        return [
            'author_id' => User::factory()->createQuietly()->getKey(),
            'calendar_id' => Calendar::factory()->create()->getKey(),
            'name' => $this->faker->word,
            'starts' => now(),
            'ends' => now()->addDay(),
            'all_day' => false,
            'repeats' => false,
        ];
    }

    /**
     * @return string[]
     */
    public function updateData(): array
    {
        return [
            'description' => $this->faker->paragraph,
        ];
    }

    public function test_can_update_event_dates_and_registration_settings(): void
    {
        $this->withToken($this->apiKey($this->scopes()['update']))
            ->patchJson(sprintf('/%s/%s', $this->endpoint(), $this->factory->getKey()), [
                'starts' => now()->addDay()->toDateTimeString(),
                'ends' => now()->addDays(2)->toDateTimeString(),
                'registration_enabled' => false,
            ])
            ->assertSuccessful();

        $this->assertFalse($this->factory->fresh()->registration_enabled);
    }

    public function test_cannot_update_event_to_end_before_it_starts(): void
    {
        $this->withToken($this->apiKey($this->scopes()['update']))
            ->patchJson(sprintf('/%s/%s', $this->endpoint(), $this->factory->getKey()), [
                'starts' => now()->addDays(2)->toDateTimeString(),
                'ends' => now()->addDay()->toDateTimeString(),
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('ends', 'error.errors');
    }
}
