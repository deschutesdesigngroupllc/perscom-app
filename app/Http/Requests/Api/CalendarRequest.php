<?php

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class CalendarRequest extends Request
{
    /**
     * @return string[]
     */
    public function commonRules(): array
    {
        return [
            'name' => 'sometimes|string',
            'description' => 'nullable|string',
            'color' => 'nullable|string',
            'timezone' => 'sometimes|string',
            'author_id' => 'sometimes|string',
        ];
    }

    /**
     * @return string[]
     */
    public function storeRules(): array
    {
        return [
            'name' => 'required',
            'timezone' => 'required',
        ];
    }
}
