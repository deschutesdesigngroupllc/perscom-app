<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Controllers\Api\Calendars\CalendarsController;
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

    /**
     * @return Factory<Calendar>
     */
    public function factory(): Factory
    {
        return Calendar::factory();
    }

    /**
     * @return string[]
     */
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

    /**
     * @return string[]
     */
    public function storeData(): array
    {
        return [
            'name' => $this->faker->word,
            'color' => $this->faker->hexColor,
        ];
    }

    /**
     * @return string[]
     */
    public function updateData(): array
    {
        return [
            'name' => $this->faker->word,
        ];
    }
}
