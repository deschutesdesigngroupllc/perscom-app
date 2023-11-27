<?php

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class RankRequest extends Request
{
    /**
     * @return string[]
     */
    public function commonRules(): array
    {
        return [
            'name' => 'string',
            'description' => 'nullable|string',
            'abbreviation' => 'nullable|string',
            'paygrade' => 'nullable|string',
            'order' => 'integer',
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
        ];
    }
}
