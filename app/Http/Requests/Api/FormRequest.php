<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class FormRequest extends Request
{
    public function commonRules(): array
    {
        return [
            'name' => 'string',
            'slug' => 'string|unique:forms,slug',
            'success_message' => 'nullable|string',
            'submission_status_id' => 'nullable|integer|exists:statuses,id',
            'is_public' => 'boolean',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'updated_at' => 'date',
            'created_at' => 'date',
            'deleted_at' => 'date',
        ];
    }

    public function storeRules(): array
    {
        return [
            'name' => 'required|string',
            'slug' => 'required|string|unique:forms,slug',
            'is_public' => 'required|boolean',
        ];
    }
}
