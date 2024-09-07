<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class TaskRequest extends Request
{
    public function commonRules(): array
    {
        return [
            'title' => 'string',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'form_id' => 'integer|exists:forms,id',
            'updated_at' => 'date',
            'created_at' => 'date',
            'deleted_at' => 'date',
        ];
    }

    public function storeRules(): array
    {
        return [
            'title' => 'required|string',
        ];
    }
}
