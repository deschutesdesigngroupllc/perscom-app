<?php

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class EventRequest extends Request
{
    /**
     * @return string[]
     */
    public function commonRules(): array
    {
        return [
            'name' => 'string',
            'calendar_id' => 'integer|exists:calendars,id',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'location' => 'nullable|string',
            'url' => 'nullable|string',
            'all_day' => 'boolean',
            'start' => 'date',
            'end' => 'date|after:start',
        ];
    }

    /**
     * @return string[]
     */
    public function storeRules(): array
    {
        return [
            'name' => 'required|string',
            'start' => 'required|date',
            'end' => 'required|date|after:start',
            'all_day' => 'required|boolean',
            'calendar_id' => 'required|integer|exists:calendars,id',
        ];
    }
}
