<?php

namespace Tests\Feature\Tenant\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Calendars\CalendarsController;
use App\Models\Calendar;
use Illuminate\Database\Eloquent\Factories\Factory;

class CalendarTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'calendars';
    }

    public function controller(): string
    {
        return CalendarsController::class;
    }

    public function model(): string
    {
        return Calendar::class;
    }

    public function factory(): Factory
    {
        return Calendar::factory();
    }

    public function scopes(): array
    {
        return [
            'index' => 'view:calendar',
            'show' => 'view:calendar',
            'store' => 'create:calendar',
            'update' => 'update:calendar',
            'delete' => 'delete:calendar',
        ];
    }

    public function storeData(): array
    {
        return [
            'name' => $this->faker->word,
        ];
    }

    public function updateData(): array
    {
        return [
            'name' => $this->faker->word,
        ];
    }
}
