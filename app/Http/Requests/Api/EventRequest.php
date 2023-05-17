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
            'name' => 'sometimes|string',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'location' => 'sometimes|string',
            'url' => 'sometimes|string',
        ];
    }

    /**
     * @return string[]
     */
    public function storeRules(): array
    {
        return [
            'name' => 'required',
            'start' => 'required|date',
            'end' => 'required|date',
            'all_day' => 'required|boolean',
            'calendar_id' => 'required|exists:calendars,id',
        ];
    }
}
