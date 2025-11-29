<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Calendars;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\CalendarRequest;
use App\Models\Calendar;
use Orion\Http\Controllers\Controller;

class CalendarsController extends Controller
{
    use AuthorizesRequests;

    protected $model = Calendar::class;

    protected $request = CalendarRequest::class;

    /**
     * @return array<int, string>
     */
    public function exposedScopes(): array
    {
        return ['tags'];
    }

    /**
     * @return array<int, string>
     */
    public function includes(): array
    {
        return ['events', 'events.author', 'tags'];
    }

    /**
     * @return array<int, string>
     */
    public function sortableBy(): array
    {
        return ['id', 'name', 'description', 'color', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function searchableBy(): array
    {
        return ['id', 'name', 'description', 'color', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'description', 'color', 'created_at', 'updated_at'];
    }
}
