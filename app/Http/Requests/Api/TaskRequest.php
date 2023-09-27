<?php

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class TaskRequest extends Request
{
    /**
     * @return string[]
     */
    public function commonRules(): array
    {
        return [
            'title' => 'string',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'form_id' => 'integer|exists:forms,id',
        ];
    }

    /**
     * @return string[]
     */
    public function storeRules(): array
    {
        return [
            'title' => 'required|string',
        ];
    }
}
