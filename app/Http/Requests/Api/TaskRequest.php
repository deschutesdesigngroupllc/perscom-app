<?php

declare(strict_types=1);

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
            'title' => 'string|max:255',
            'description' => 'string|max:65535',
            'instructions' => 'nullable|string|max:65535',
            'form_id' => 'nullable|integer|exists:forms,id',
            'updated_at' => 'date',
            'created_at' => 'date',
            'deleted_at' => 'nullable|date',
        ];
    }

    /**
     * @return string[]
     */
    public function storeRules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:65535',
        ];
    }
}
