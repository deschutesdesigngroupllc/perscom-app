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
            'text_color' => 'string',
            'bg_color' => 'string',
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
            'text_color' => 'required|string',
            'bg_color' => 'required|string',
        ];
    }
}
