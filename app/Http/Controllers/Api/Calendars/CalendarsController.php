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

    public function exposedScopes(): array
    {
        return ['tags'];
    }

    public function includes(): array
    {
        return ['events', 'events.author', 'tags'];
    }

    public function sortableBy(): array
    {
        return ['id', 'name', 'description', 'color', 'created_at', 'updated_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'name', 'description', 'color', 'created_at', 'updated_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'name', 'description', 'color', 'created_at', 'updated_at'];
    }
}
