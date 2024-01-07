<?php

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class StatusRequest extends Request
{
    /**
     * @return string[]
     */
    public function commonRules(): array
    {
        return [
            'name' => 'string',
            'text_color' => ['string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'bg_color' => ['string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'updated_at' => 'date',
            'created_at' => 'date',
        ];
    }

    /**
     * @return string[]
     */
    public function storeRules(): array
    {
        return [
            'name' => 'required|string',
            'text_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'bg_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
        ];
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'bg_color' => 'background color',
        ];
    }
}
